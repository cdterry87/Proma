<?php

declare(strict_types=1);

use App\Enums\IssuePriority;
use App\Enums\NotificationEvent;
use App\Jobs\SendIssueNotifications;
use App\Jobs\SendProjectNotifications;
use App\Jobs\SendTaskNotifications;
use App\Models\Issue;
use App\Models\NotificationLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\RecordNotification;
use App\Support\Notifier;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    $this->travelTo('2026-06-15 02:00:00');
    $this->user = User::factory()->create();
});

function runNotificationJobs(): void
{
    SendIssueNotifications::dispatchSync();
    SendProjectNotifications::dispatchSync();
    SendTaskNotifications::dispatchSync();
}

describe('stale issues', function () {
    it('notifies once an issue has been open longer than its priority allows', function (IssuePriority $priority, int $days, string $title, NotificationEvent $event) {
        $issue = Issue::factory()->for($this->user)->create(['priority' => $priority, 'created_at' => now()->subDays($days)->subMinute()]);

        SendIssueNotifications::dispatchSync();

        $notification = $this->user->userNotifications()->sole();

        expect($notification->title)->toBe($title)
            ->and($notification->message)->toContain($issue->name)->toContain("more than {$days} days")
            ->and($notification->subject->is($issue))->toBeTrue()
            ->and(NotificationLog::query()->sole()->event)->toBe($event);
    })->with([
        [IssuePriority::Low, 30, 'Low Priority Issue', NotificationEvent::IssueStaleLow],
        [IssuePriority::Medium, 14, 'Medium Issue', NotificationEvent::IssueStaleMedium],
        [IssuePriority::High, 7, 'High Issue', NotificationEvent::IssueStaleHigh],
        [IssuePriority::Critical, 3, 'Critical Issue', NotificationEvent::IssueStaleCritical],
    ]);

    it('waits until the issue is past its limit', function () {
        Issue::factory()->for($this->user)->create(['priority' => IssuePriority::Critical, 'created_at' => now()->subDays(3)]);

        SendIssueNotifications::dispatchSync();

        expect(UserNotification::query()->count())->toBe(0);
    });

    it('skips resolved issues', function () {
        Issue::factory()->for($this->user)->resolved()->create(['priority' => IssuePriority::Critical, 'created_at' => now()->subDays(40)]);

        SendIssueNotifications::dispatchSync();

        expect(UserNotification::query()->count())->toBe(0);
    });

    it('notifies again when the priority changes', function () {
        $issue = Issue::factory()->for($this->user)->create(['priority' => IssuePriority::Medium, 'created_at' => now()->subDays(20)]);

        SendIssueNotifications::dispatchSync();
        $issue->update(['priority' => IssuePriority::High]);
        SendIssueNotifications::dispatchSync();

        expect($this->user->userNotifications()->pluck('title')->all())->toBe(['Medium Issue', 'High Issue']);
    });
});

describe('due dates', function () {
    it('reminds about a project anywhere inside the window, so a missed run cannot skip it', function (int $days, int $expected) {
        Project::factory()->for($this->user)->create(['due_date' => today()->addDays($days)]);

        SendProjectNotifications::dispatchSync();

        expect($this->user->userNotifications()->where('title', 'Project Due Reminder')->count())->toBe($expected);
    })->with([
        'today' => [0, 1],
        'in five days' => [5, 1],
        'last day of the window' => [10, 1],
        'past the window' => [11, 0],
    ]);

    it('uses the configured window in the reminder text', function () {
        config(['proma.due_reminder_days' => 3]);
        $project = Project::factory()->for($this->user)->create(['name' => 'Apollo', 'due_date' => today()->addDays(3)]);
        Project::factory()->for($this->user)->create(['due_date' => today()->addDays(4)]);

        SendProjectNotifications::dispatchSync();

        expect($this->user->userNotifications()->sole())
            ->message->toBe('The project "Apollo" is due within 3 days, on Jun 18, 2026.')
            ->subject->is($project)->toBeTrue();
    });

    it('sends an overdue project only the overdue notification', function () {
        Project::factory()->for($this->user)->create(['name' => 'Apollo', 'due_date' => today()->subDay()]);

        SendProjectNotifications::dispatchSync();

        expect($this->user->userNotifications()->sole())
            ->title->toBe('Project Overdue')
            ->message->toBe('The project "Apollo" was due on Jun 14, 2026 and is not complete.');
    });

    it('sends both notifications as a project goes from due to overdue', function () {
        Project::factory()->for($this->user)->create(['due_date' => today()->addDay()]);

        SendProjectNotifications::dispatchSync();
        $this->travel(5)->days();
        SendProjectNotifications::dispatchSync();

        expect($this->user->userNotifications()->pluck('title')->all())->toBe(['Project Due Reminder', 'Project Overdue']);
    });

    it('skips completed and undated projects', function () {
        Project::factory()->for($this->user)->completed()->create(['due_date' => today()->subDay()]);
        Project::factory()->for($this->user)->completed()->create(['due_date' => today()->addDay()]);
        Project::factory()->for($this->user)->create(['due_date' => null]);

        SendProjectNotifications::dispatchSync();

        expect(UserNotification::query()->count())->toBe(0);
    });

    it('names a task by its title and notifies the project owner', function () {
        $project = Project::factory()->for($this->user)->create(['name' => 'Apollo']);
        $due = Task::factory()->forProject($project)->create(['title' => 'Write copy', 'due_date' => today()->addDays(2)]);
        $late = Task::factory()->forProject($project)->create(['title' => 'Fix footer', 'due_date' => today()->subDay()]);

        SendTaskNotifications::dispatchSync();

        $notifications = $this->user->userNotifications()->with('subject')->get()->keyBy('title');

        expect($notifications['Project Task Due Reminder']->message)->toBe('The task "Write copy" on project "Apollo" is due within 10 days, on Jun 17, 2026.')
            ->and($notifications['Project Task Due Reminder']->subject->is($due))->toBeTrue()
            ->and($notifications['Project Task Overdue']->message)->toBe('The task "Fix footer" on project "Apollo" was due on Jun 14, 2026 and is not complete.')
            ->and($notifications['Project Task Overdue']->subject->is($late))->toBeTrue();
    });

    it('never notifies about issue tasks', function () {
        $issue = Issue::factory()->for($this->user)->create();
        Task::factory()->forIssue($issue)->create(['due_date' => today()->subDay()]);
        Task::factory()->forIssue($issue)->create(['due_date' => today()->addDay()]);

        SendTaskNotifications::dispatchSync();

        expect(UserNotification::query()->count())->toBe(0);
    });

    it('skips completed tasks', function () {
        $project = Project::factory()->for($this->user)->create();
        Task::factory()->forProject($project)->completed()->create(['due_date' => today()->subDay()]);

        SendTaskNotifications::dispatchSync();

        expect(UserNotification::query()->count())->toBe(0);
    });
});

it('generates each notification once, for its owner, across repeated runs', function () {
    $other = User::factory()->create();
    Project::factory()->for($this->user)->create(['due_date' => today()->subDay()]);
    Issue::factory()->for($other)->create(['priority' => IssuePriority::Critical, 'created_at' => now()->subDays(5)]);
    Task::factory()->forProject(Project::factory()->for($other)->create())->create(['due_date' => today()->subDay()]);

    runNotificationJobs();
    runNotificationJobs();

    expect($this->user->userNotifications()->count())->toBe(1)
        ->and($other->userNotifications()->count())->toBe(2)
        ->and(NotificationLog::query()->count())->toBe(3);

    Notification::assertSentTimes(RecordNotification::class, 3);
});

it('writes nothing when the log already holds the event', function () {
    $project = Project::factory()->for($this->user)->create();
    $notifier = app(Notifier::class);

    expect($notifier->once($project, NotificationEvent::ProjectDue, $this->user, 'First', 'Message'))->toBeTrue()
        ->and($notifier->once($project, NotificationEvent::ProjectDue, $this->user, 'Second', 'Message'))->toBeFalse()
        ->and($this->user->userNotifications()->pluck('title')->all())->toBe(['First']);
});

it('keeps a notification without a link when its record is deleted', function () {
    $project = Project::factory()->for($this->user)->create(['due_date' => today()->subDay()]);

    SendProjectNotifications::dispatchSync();
    $project->delete();

    expect($this->user->userNotifications()->sole()->subject)->toBeNull()
        ->and(NotificationLog::query()->count())->toBe(0);
});

it('schedules each job at its documented time', function (string $job, string $expression) {
    $event = collect(app(Schedule::class)->events())->firstWhere('description', $job);

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe($expression)
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
})->with([
    [SendIssueNotifications::class, '0 2 * * *'],
    [SendProjectNotifications::class, '0 4 * * *'],
    [SendTaskNotifications::class, '0 6 * * *'],
]);
