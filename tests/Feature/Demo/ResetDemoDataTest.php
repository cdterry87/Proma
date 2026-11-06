<?php

declare(strict_types=1);

use App\Actions\ResetDemoData;
use App\Jobs\ResetDemoUsers;
use App\Jobs\SendProjectNotifications;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\NotificationLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\RecordNotification;
use App\Support\DashboardMetrics;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    config(['proma.demo' => ['enabled' => true, 'email' => 'demo@example.com', 'password' => 'demo-secret']]);
    $this->demo = User::factory()->demo()->create(['email' => 'demo@example.com']);
    $this->reset = app(ResetDemoData::class);
});

it('refuses to reset a real user and leaves their data alone', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    expect(fn () => $this->reset->handle($user))->toThrow(InvalidArgumentException::class);

    expect($client->fresh())->not->toBeNull()
        ->and($user->clients()->count())->toBe(1);
});

it('leaves real users untouched during the nightly job', function () {
    $user = User::factory()->create(['name' => 'Real Person']);
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->forProject($project)->create();
    $notification = UserNotification::factory()->for($user)->create();

    ResetDemoUsers::dispatchSync();

    expect($user->fresh()->name)->toBe('Real Person')
        ->and($project->fresh())->not->toBeNull()
        ->and($task->fresh())->not->toBeNull()
        ->and($notification->fresh())->not->toBeNull()
        ->and($user->clients()->count())->toBe(1);
});

it('restores the profile and password [D-14]', function () {
    $this->demo->update(['name' => 'Scribble', 'title' => 'Vandal', 'password' => 'changed', 'email_notifications' => true]);

    $this->reset->handle($this->demo);

    $demo = $this->demo->fresh();

    expect($demo->name)->toBe('Demo User')
        ->and($demo->title)->toBe('Project Manager')
        ->and($demo->email_notifications)->toBeFalse()
        ->and($demo->is_demo)->toBeTrue()
        ->and($demo->email)->toBe('demo@example.com')
        ->and(Hash::check('demo-secret', $demo->password))->toBeTrue();
});

it('keeps the password when none is configured', function () {
    config(['proma.demo.password' => null]);
    $this->demo->update(['password' => 'current-one']);

    $this->reset->handle($this->demo);

    expect(Hash::check('current-one', $this->demo->fresh()->password))->toBeTrue();
});

it('replaces everything a visitor created', function () {
    $client = Client::factory()->for($this->demo)->create(['name' => 'Visitor client']);
    ClientContact::factory()->for($client)->create(['name' => 'Visitor contact']);
    $project = Project::factory()->forClient($client)->create(['name' => 'Visitor project']);
    Task::factory()->forProject($project)->create(['title' => 'Visitor task']);
    $issue = Issue::factory()->forClient($client)->create(['name' => 'Visitor issue']);
    Task::factory()->forIssue($issue)->create(['title' => 'Visitor issue task']);
    UserNotification::factory()->for($this->demo)->create(['title' => 'Visitor notification']);

    $this->reset->handle($this->demo);

    expect(Client::query()->where('name', 'Visitor client')->exists())->toBeFalse()
        ->and(ClientContact::query()->where('name', 'Visitor contact')->exists())->toBeFalse()
        ->and(Project::query()->where('name', 'Visitor project')->exists())->toBeFalse()
        ->and(Issue::query()->where('name', 'Visitor issue')->exists())->toBeFalse()
        ->and(Task::query()->where('title', 'like', 'Visitor%')->exists())->toBeFalse()
        ->and(UserNotification::query()->where('title', 'Visitor notification')->exists())->toBeFalse();
});

it('creates the same amount of data each time, with no orphans', function () {
    $this->reset->handle($this->demo);

    $counts = fn () => [
        $this->demo->clients()->count(),
        $this->demo->projects()->count(),
        $this->demo->issues()->count(),
        Task::query()->count(),
        $this->demo->userNotifications()->count(),
        NotificationLog::query()->count(),
    ];

    $first = $counts();
    $this->reset->handle($this->demo);

    expect($counts())->toBe($first)
        ->and(array_slice($first, 0, 3))->toBe([8, 15, 30]);
});

it('gives every client a contact and every project tasks', function () {
    $this->reset->handle($this->demo);

    expect($this->demo->clients()->doesntHave('contacts')->count())->toBe(0)
        ->and($this->demo->projects()->doesntHave('tasks')->count())->toBe(0);
});

it('reseeds each demo user [D-14]', function () {
    $second = User::factory()->demo()->create();

    ResetDemoUsers::dispatchSync();

    expect($this->demo->clients()->count())->toBe(8)
        ->and($second->clients()->count())->toBe(8);
});

it('spreads dates around today so that every feature has content', function () {
    $this->reset->handle($this->demo);

    expect($this->demo->projects()->overdue()->count())->toBeGreaterThan(0)
        ->and($this->demo->projects()->dueWithin(10)->count())->toBeGreaterThan(0)
        ->and($this->demo->projects()->completed()->count())->toBeGreaterThan(0)
        ->and($this->demo->issues()->resolved()->count())->toBeGreaterThan(0)
        ->and($this->demo->issues()->unresolved()->count())->toBeGreaterThan(0)
        ->and($this->demo->clients()->where('active', false)->count())->toBe(1);
});

it('keeps dates in a sensible order', function () {
    $this->reset->handle($this->demo);

    $projects = $this->demo->projects()->get();
    $issues = $this->demo->issues()->get();

    foreach ($projects as $project) {
        expect($project->start_date->gte($project->created_at->startOfDay()))->toBeTrue($project->name)
            ->and($project->completed_date === null || $project->completed_date->gte($project->start_date))->toBeTrue($project->name);
    }

    foreach ($issues as $issue) {
        expect($issue->resolved_date === null || $issue->resolved_date->gte($issue->created_at->startOfDay()))->toBeTrue($issue->name);

        if ($issue->project_id !== null) {
            expect(Project::query()->findOrFail($issue->project_id)->client_id)->toBe($issue->client_id);
        }
    }
});

it('generates every kind of notification straight away and sends no email', function () {
    Notification::fake();
    Queue::fake();

    $this->reset->handle($this->demo);

    $titles = $this->demo->userNotifications()->distinct()->pluck('title')->all();

    expect($titles)->toContain(
        'Critical Issue',
        'High Issue',
        'Medium Issue',
        'Low Priority Issue',
        'Project Due Reminder',
        'Project Overdue',
        'Project Task Due Reminder',
        'Project Task Overdue',
    );

    Notification::assertNothingSent();
    Queue::assertNothingPushed();
});

it('only notifies the user being reset', function () {
    Notification::fake();
    $user = User::factory()->create();
    Project::factory()->for($user)->create(['due_date' => today()->subDay()]);
    Task::factory()->forProject(Project::factory()->for($user)->create())->create(['due_date' => today()->subDay()]);

    $this->reset->handle($this->demo);

    expect($user->userNotifications()->count())->toBe(0);
    Notification::assertNotSentTo($user, RecordNotification::class);
});

it('adds nothing when the nightly notification jobs run afterwards', function () {
    $this->reset->handle($this->demo);
    $count = $this->demo->userNotifications()->count();

    SendProjectNotifications::dispatchSync();

    expect($this->demo->userNotifications()->count())->toBe($count);
});

it('fills the dashboard for every timeframe', function (int $days) {
    $this->reset->handle($this->demo);

    $metrics = new DashboardMetrics($this->demo, $days);

    expect(collect($metrics->stats())->sum('value'))->toBeGreaterThan(0)
        ->and($metrics->incompleteProjects())->not->toBeEmpty()
        ->and($metrics->unresolvedIssues())->not->toBeEmpty();

    Livewire::actingAs($this->demo)->test(Dashboard::class, ['days' => (string) $days])->assertOk();
})->with(DashboardMetrics::TIMEFRAMES);

it('rolls back when the reset fails', function () {
    $client = Client::factory()->for($this->demo)->create(['name' => 'Survivor']);

    User::updating(fn () => throw new RuntimeException('Reset failed'));

    expect(fn () => $this->reset->handle($this->demo))->toThrow(RuntimeException::class, 'Reset failed');

    expect($client->fresh())->not->toBeNull();
});

it('schedules the reset at midnight', function () {
    $event = collect(app(Schedule::class)->events())->firstWhere('description', ResetDemoUsers::class);

    expect($event->expression)->toBe('0 0 * * *')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
});

it('keeps Faker available without dev dependencies [D-15]', function () {
    $composer = json_decode((string) file_get_contents(base_path('composer.json')), true);

    expect($composer['require'])->toHaveKey('fakerphp/faker');
});

describe('seeder', function () {
    beforeEach(function () {
        $this->demo->delete();
    });

    it('creates and fills the demo account', function () {
        $this->seed(DemoUserSeeder::class);

        $user = User::query()->where('email', 'demo@example.com')->sole();

        expect($user->is_demo)->toBeTrue()
            ->and($user->email_notifications)->toBeFalse()
            ->and($user->clients()->count())->toBe(8)
            ->and(Hash::check('demo-secret', $user->password))->toBeTrue();
    });

    it('resets an existing demo account instead of duplicating it', function () {
        $this->seed(DemoUserSeeder::class);
        $this->seed(DemoUserSeeder::class);

        expect(User::query()->count())->toBe(1)
            ->and(Client::query()->count())->toBe(8);
    });

    it('never touches a real account with the demo email', function () {
        $user = User::factory()->create(['email' => 'demo@example.com', 'name' => 'Real Person']);
        Client::factory()->for($user)->create();

        $this->seed(DemoUserSeeder::class);

        expect($user->fresh()->is_demo)->toBeFalse()
            ->and($user->fresh()->name)->toBe('Real Person')
            ->and(Client::query()->count())->toBe(1);
    });

    it('does nothing when the demo is not configured [D-8]', function () {
        config(['proma.demo.email' => null]);

        $this->seed(DatabaseSeeder::class);

        expect(User::query()->count())->toBe(0);
    });
});

it('lets a demo visitor sign in after a reset and sends them home', function () {
    $this->reset->handle($this->demo);
    session(['url.intended' => url('/clients/an-old-record')]);

    Livewire::test(Login::class)
        ->set('email', 'demo@example.com')
        ->set('password', 'demo-secret')
        ->call('login')
        ->assertRedirect(route('dashboard'));
});

it('still returns a real user to the page they wanted', function () {
    $user = User::factory()->create(['password' => 'a-real-password']);
    session(['url.intended' => url('/clients')]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'a-real-password')
        ->call('login')
        ->assertRedirect(url('/clients'));
});
