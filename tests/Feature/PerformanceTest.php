<?php

declare(strict_types=1);

use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Clients\Show as ClientsShow;
use App\Livewire\Contacts\Manager as ContactsManager;
use App\Livewire\Dashboard;
use App\Livewire\Issues\Index as IssuesIndex;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Tasks\Manager as TasksManager;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

function seedRows(User $user, int $count): Client
{
    $client = Client::factory()->for($user)->create();

    ClientContact::factory()->count($count)->for($client)->create();
    $project = Project::factory()->forClient($client)->create();
    Project::factory()->count($count)->forClient($client)->create();
    Issue::factory()->count($count)->forClient($client)->create(['project_id' => $project->id]);
    Task::factory()->count($count)->for($project, 'taskable')->create();
    Client::factory()->count($count)->for($user)->create();
    UserNotification::factory()->count($count)->for($user)->create(['subject_type' => 'project', 'subject_id' => $project->id]);

    return $client;
}

function countQueries(Closure $render): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();
    $render();
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $count;
}

dataset('pages', [
    'clients' => [fn (Client $client) => Livewire::test(ClientsIndex::class)],
    'client' => [fn (Client $client) => Livewire::test(ClientsShow::class, ['client' => $client])],
    'contacts' => [fn (Client $client) => Livewire::test(ContactsManager::class, ['client' => $client])],
    'projects' => [fn (Client $client) => Livewire::test(ProjectsIndex::class)],
    'issues' => [fn (Client $client) => Livewire::test(IssuesIndex::class)],
    'tasks' => [fn (Client $client) => Livewire::test(TasksManager::class, ['taskable' => $client->projects()->first()])],
    'notifications' => [fn (Client $client) => Livewire::test(NotificationsIndex::class)],
    'dashboard' => [fn (Client $client) => Livewire::test(Dashboard::class)],
]);

it('runs the same number of queries however many rows a page shows [D-31]', function (Closure $render) {
    $small = User::factory()->create();
    $smallClient = seedRows($small, 2);
    $large = User::factory()->create();
    $largeClient = seedRows($large, 12);

    // The first render warms caches that would otherwise count against it.
    $this->actingAs($small);
    $render($smallClient);
    $few = countQueries(fn () => $render($smallClient));

    $this->actingAs($large);
    $many = countQueries(fn () => $render($largeClient));

    expect($many)->toBe($few);
})->with('pages');

it('keeps the dashboard to a small number of queries', function () {
    $user = User::factory()->create();
    seedRows($user, 12);
    $this->actingAs($user);

    expect(countQueries(fn () => Livewire::test(Dashboard::class)))->toBeLessThanOrEqual(20);
});
