<?php

declare(strict_types=1);

use App\Actions\ResetDemoData;
use App\Enums\DemoRecord;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Contacts\Manager as ContactsManager;
use App\Livewire\Issues\Index as IssuesIndex;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Tasks\Manager as TasksManager;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

/**
 * One record of each kind, owned by the user, and a component that adds one more.
 *
 * @return array<string, array{DemoRecord, Closure(User): Testable}>
 */
function demoCreators(): array
{
    return [
        'client' => [DemoRecord::Client, fn (User $user) => Livewire::test(ClientsIndex::class)
            ->set('form.name', 'New client')
            ->set('form.description', 'A client.')],
        'project' => [DemoRecord::Project, fn (User $user) => Livewire::test(ProjectsIndex::class)
            ->set('form.name', 'New project')
            ->set('form.description', 'A project.')
            ->set('form.client_id', (string) $user->clients()->value('id'))],
        'issue' => [DemoRecord::Issue, fn (User $user) => Livewire::test(IssuesIndex::class)
            ->set('form.name', 'New issue')
            ->set('form.description', 'An issue.')
            ->set('form.priority', '1')
            ->set('form.client_id', (string) $user->clients()->value('id'))],
        'contact' => [DemoRecord::Contact, fn (User $user) => Livewire::test(ContactsManager::class, ['client' => $user->clients()->first()])
            ->set('form.name', 'New contact')
            ->set('form.title', 'Manager')
            ->set('form.email', 'new@example.com')],
        'task' => [DemoRecord::Task, fn (User $user) => Livewire::test(TasksManager::class, ['taskable' => $user->projects()->first()])
            ->set('form.title', 'New task')
            ->set('form.description', 'A task.')],
    ];
}

function userWithOneOfEach(bool $demo): User
{
    $user = User::factory()->create(['is_demo' => $demo]);
    $client = Client::factory()->for($user)->create();
    ClientContact::factory()->for($client)->create();
    $project = Project::factory()->for($user)->for($client)->create();
    Issue::factory()->for($user)->for($client)->create(['project_id' => null]);
    Task::factory()->for($project, 'taskable')->create();

    return $user;
}

beforeEach(function () {
    config(['proma.demo.limits' => ['clients' => 1, 'projects' => 1, 'issues' => 1, 'contacts' => 1, 'tasks' => 1]]);
});

it('stops a demo account at its cap', function (DemoRecord $record, Closure $creator) {
    $user = userWithOneOfEach(demo: true);
    $this->actingAs($user);

    $creator($user)
        ->call('save')
        ->assertDispatched('toast', type: 'error', message: "The demo allows up to 1 {$record->value}. Delete one to add another.");

    expect($record->count($user))->toBe(1);
})->with(demoCreators());

it('lets a demo account add records below its cap', function (DemoRecord $record, Closure $creator) {
    config(["proma.demo.limits.{$record->value}" => 2]);
    $user = userWithOneOfEach(demo: true);
    $this->actingAs($user);

    $creator($user)->call('save')->assertHasNoErrors()->assertDispatched('toast', type: 'success');

    expect($record->count($user))->toBe(2);
})->with(demoCreators());

it('never caps regular accounts', function (DemoRecord $record, Closure $creator) {
    $user = userWithOneOfEach(demo: false);
    $this->actingAs($user);

    $creator($user)->call('save')->assertHasNoErrors()->assertDispatched('toast', type: 'success');

    expect($record->count($user))->toBe(2);
})->with(demoCreators());

it('counts only the demo account\'s own records', function (DemoRecord $record) {
    $demo = userWithOneOfEach(demo: true);
    userWithOneOfEach(demo: false);

    expect($record->count($demo))->toBe(1);
})->with(DemoRecord::cases());

it('lets a demo account at its cap edit tasks and contacts', function () {
    $user = userWithOneOfEach(demo: true);
    $this->actingAs($user);
    $task = Task::query()->sole();
    $contact = ClientContact::query()->sole();

    Livewire::test(TasksManager::class, ['taskable' => $user->projects()->first()])
        ->call('edit', $task->uuid)
        ->set('form.title', 'Renamed task')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test(ContactsManager::class, ['client' => $user->clients()->first()])
        ->call('edit', $contact->uuid)
        ->set('form.name', 'Renamed contact')
        ->call('save')
        ->assertHasNoErrors();

    expect($task->fresh()->title)->toBe('Renamed task')
        ->and($contact->fresh()->name)->toBe('Renamed contact');
});

it('fits the demo dataset within the default caps', function () {
    $defaults = (require config_path('proma.php'))['demo']['limits'];
    config(['proma.demo.limits' => $defaults]);
    $user = User::factory()->create(['is_demo' => true]);

    app(ResetDemoData::class)->handle($user);

    foreach (DemoRecord::cases() as $record) {
        expect($record->count($user))->toBeLessThan(intdiv($record->limit(), 2));
    }
});
