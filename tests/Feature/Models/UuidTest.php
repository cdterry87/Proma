<?php

declare(strict_types=1);

use App\Livewire\Notifications\Index;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('generates a unique uuid when a record is created and keeps it on update', function (Closure $create) {
    $first = $create();
    $second = $create();
    $uuid = $first->uuid;

    $first->touch();

    expect(Str::isUuid($uuid))->toBeTrue()
        ->and($second->uuid)->not->toBe($uuid)
        ->and($first->fresh()->uuid)->toBe($uuid)
        ->and($first->getKeyName())->toBe('id')
        ->and($first->id)->toBeInt();
})->with([
    'client' => fn () => Client::factory()->create(),
    'contact' => fn () => ClientContact::factory()->create(),
    'project' => fn () => Project::factory()->create(),
    'issue' => fn () => Issue::factory()->create(),
    'task' => fn () => Task::factory()->create(),
    'notification' => fn () => UserNotification::factory()->create(),
]);

it('cannot be set through mass assignment', function () {
    Client::factory()->create()->update(['uuid' => (string) Str::uuid7()]);
})->throws(MassAssignmentException::class);

describe('record pages', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('resolve by uuid and not by id', function (string $path, Closure $create) {
        $record = $create($this->user);

        expect(route("{$path}.show", $record))->toEndWith("/{$path}/{$record->uuid}");

        $this->get("/{$path}/{$record->uuid}")->assertOk();
        $this->get("/{$path}/{$record->id}")->assertNotFound();
        $this->get("/{$path}/not-a-uuid")->assertNotFound();
    })->with([
        'client' => ['clients', fn (User $user) => Client::factory()->for($user)->create()],
        'project' => ['projects', fn (User $user) => Project::factory()->for($user)->create()],
        'issue' => ['issues', fn (User $user) => Issue::factory()->for($user)->create()],
    ]);

    it('do not show another user\'s record through its uuid', function (string $path, Closure $create) {
        $this->get("/{$path}/{$create()->uuid}")->assertNotFound();
    })->with([
        'client' => ['clients', fn () => Client::factory()->create()],
        'project' => ['projects', fn () => Project::factory()->create()],
        'issue' => ['issues', fn () => Issue::factory()->create()],
    ]);

    it('link to the filtered lists by client uuid', function () {
        $client = Client::factory()->for($this->user)->create();

        $this->get("/clients/{$client->uuid}")
            ->assertSee('filters%5Bclient%5D='.$client->uuid, false)
            ->assertDontSee('filters%5Bclient%5D='.$client->id.'"', false);
    });

    it('ignore a client id in a URL filter', function () {
        $client = Client::factory()->for($this->user)->create();
        Project::factory()->for($this->user)->create(['name' => 'Elsewhere']);

        $this->get('/projects?filters[client]='.$client->id)->assertSee('Elsewhere');
        $this->get('/projects?filters[client]='.$client->uuid)->assertDontSee('Elsewhere');
    });
});

it('renders no integer ids in record actions', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $contact = ClientContact::factory()->for($client)->create();

    $this->actingAs($user)->get("/clients/{$client->uuid}")
        ->assertSee("edit('{$contact->uuid}')")
        ->assertDontSee("edit({$contact->id})")
        ->assertDontSeeHtml("contact-{$contact->id}\"");
});

it('finds no record by an integer id through an action', function () {
    $user = User::factory()->create();
    $notification = UserNotification::factory()->for($user)->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('markRead', (string) $notification->id)
        ->assertNotFound();

    expect($notification->fresh()->isRead())->toBeFalse();
});
