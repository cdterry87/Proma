<?php

declare(strict_types=1);

use App\Livewire\Clients\Show;
use App\Livewire\Contacts\Manager;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->client = Client::factory()->for($this->user)->create(['name' => 'Acme', 'description' => 'About Acme']);
    $this->actingAs($this->user);
});

it('renders the client with its contacts manager', function () {
    $this->get("/clients/{$this->client->id}")
        ->assertOk()
        ->assertSeeLivewire(Show::class)
        ->assertSeeLivewire(Manager::class)
        ->assertSee('Acme')
        ->assertSee('About Acme');
});

it('returns 404 for another user\'s client and for guests a redirect', function () {
    $other = Client::factory()->create();

    $this->get("/clients/{$other->id}")->assertNotFound();

    auth()->logout();

    $this->get("/clients/{$other->id}")->assertRedirect('/login');
});

it('shows counts and related records', function () {
    Project::factory()->forClient($this->client)->create(['name' => 'Open project']);
    Project::factory()->forClient($this->client)->completed()->create(['name' => 'Done project']);
    Issue::factory()->forClient($this->client)->create(['name' => 'Open issue']);
    Issue::factory()->forClient($this->client)->resolved()->create(['name' => 'Fixed issue']);

    Livewire::test(Show::class, ['client' => $this->client])
        ->assertViewHas('incompleteProjectCount', 1)
        ->assertViewHas('completedProjectCount', 1)
        ->assertViewHas('unresolvedIssueCount', 1)
        ->assertViewHas('resolvedIssueCount', 1)
        ->assertSee('Open project')
        ->assertDontSee('Done project')
        ->assertSee('Open issue')
        ->assertDontSee('Fixed issue');
});

it('orders related projects with the nearest due date first and no date last', function () {
    Project::factory()->forClient($this->client)->create(['name' => 'None', 'due_date' => null]);
    Project::factory()->forClient($this->client)->create(['name' => 'Later', 'due_date' => today()->addDays(9)]);
    Project::factory()->forClient($this->client)->create(['name' => 'Soon', 'due_date' => today()->addDay()]);

    $names = Livewire::test(Show::class, ['client' => $this->client])->viewData('projects')->pluck('name')->all();

    expect($names)->toBe(['Soon', 'Later', 'None']);
});

it('labels a missing due date rather than showing today', function () {
    Project::factory()->forClient($this->client)->create(['due_date' => null]);

    Livewire::test(Show::class, ['client' => $this->client])->assertSee('No due date');
});

it('collapses long descriptions behind show more', function () {
    $this->client->update(['description' => str_repeat('word ', 60)]);

    Livewire::test(Show::class, ['client' => $this->client])->assertSee('Show more');

    $this->client->update(['description' => 'Short']);

    Livewire::test(Show::class, ['client' => $this->client])->assertDontSee('Show more');
});

it('updates the client', function () {
    Livewire::test(Show::class, ['client' => $this->client])
        ->assertSet('form.name', 'Acme')
        ->set('form.name', 'Renamed')
        ->set('form.active', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-dialog', name: 'client-form')
        ->assertSee('Renamed');

    $client = $this->client->fresh();

    expect($client->name)->toBe('Renamed')
        ->and($client->active)->toBeFalse()
        ->and($client->user_id)->toBe($this->user->id);
});

it('validates updates', function () {
    Livewire::test(Show::class, ['client' => $this->client])
        ->set('form.name', '')
        ->set('form.description', '')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.description'])
        ->assertNotDispatched('close-dialog');

    expect($this->client->fresh()->name)->toBe('Acme');
});

it('restores saved values when the dialog closes', function () {
    Livewire::test(Show::class, ['client' => $this->client])
        ->set('form.name', 'Unsaved')
        ->dispatch('dialog-closed', name: 'client-form')
        ->assertSet('form.name', 'Acme');
});

it('deletes the client and everything beneath it', function () {
    $project = Project::factory()->forClient($this->client)->create();
    Task::factory()->forProject($project)->create();
    ClientContact::factory()->for($this->client)->create();

    Livewire::test(Show::class, ['client' => $this->client])
        ->assertSee('its contacts (1), projects (1), issues (0)')
        ->call('delete')
        ->assertRedirect('/clients');

    expect(Client::query()->count())->toBe(0)
        ->and(Task::query()->count())->toBe(0)
        ->and(session('toast.type'))->toBe('success');
});

it('locks the record', function () {
    Livewire::test(Show::class, ['client' => $this->client])->set('client', Client::factory()->create());
})->throws('Cannot update locked property');

describe('another user', function () {
    beforeEach(function () {
        $this->component = Livewire::test(Show::class, ['client' => $this->client])->set('form.name', 'Stolen');
        $this->actingAs(User::factory()->create());
    });

    it('cannot save', function () {
        $this->component->call('save')->assertForbidden();

        expect($this->client->fresh()->name)->toBe('Acme');
    });

    it('cannot delete', function () {
        $this->component->call('delete')->assertForbidden();

        expect($this->client->fresh())->not->toBeNull();
    });

    it('cannot mount the component', function () {
        Livewire::test(Show::class, ['client' => $this->client])->assertNotFound();
    });
});
