<?php

declare(strict_types=1);

use App\Livewire\Contacts\Manager;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->client = Client::factory()->for($this->user)->create();
    $this->actingAs($this->user);
    $this->manager = fn () => Livewire::test(Manager::class, ['client' => $this->client]);
});

function validContact(array $overrides = []): array
{
    return [
        'form.name' => 'Ada',
        'form.title' => 'Engineer',
        'form.email' => 'ada@example.com',
        ...$overrides,
    ];
}

it('lists only this client\'s contacts', function () {
    ClientContact::factory()->for($this->client)->create(['name' => 'Mine']);
    ClientContact::factory()->create(['name' => 'Other client']);

    ($this->manager)()
        ->assertSee('Mine')
        ->assertDontSee('Other client');
});

it('searches name, title, and email', function (string $term) {
    ClientContact::factory()->for($this->client)->create(['name' => 'Ada', 'title' => 'Engineer', 'email' => 'lovelace@example.com']);
    ClientContact::factory()->for($this->client)->create(['name' => 'Bob', 'title' => 'Sales', 'email' => 'bob@example.com']);

    $rows = ($this->manager)()->set('search', $term)->viewData('rows');

    expect($rows->pluck('name')->all())->toBe(['Ada']);
})->with(['Ada', 'Engineer', 'lovelace']);

it('filters by status and sorts', function () {
    ClientContact::factory()->for($this->client)->create(['name' => 'A', 'active' => true]);
    ClientContact::factory()->for($this->client)->create(['name' => 'B', 'active' => false]);

    expect(($this->manager)()->set('filters.active', '0')->viewData('rows')->pluck('name')->all())->toBe(['B'])
        ->and(($this->manager)()->call('sortBy', 'name')->viewData('rows')->pluck('name')->all())->toBe(['B', 'A']);
});

it('exports only this client\'s contacts', function () {
    ClientContact::factory()->for($this->client)->create(['name' => 'Mine']);
    ClientContact::factory()->create(['name' => 'Other client']);

    $component = ($this->manager)()->call('export', 'csv');

    expect(base64_decode(data_get($component->effects, 'download.content')))
        ->toContain('Mine')
        ->not->toContain('Other client');
});

it('opens an empty form to add a contact', function () {
    ($this->manager)()
        ->call('create')
        ->assertDispatched('open-dialog', name: 'contact-form')
        ->assertSet('editingUuid', null);
});

it('adds a contact, then closes and resets the form', function () {
    ($this->manager)()
        ->set(validContact(['form.phone' => '', 'form.phone_ext' => '']))
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-dialog', name: 'contact-form')
        ->assertDispatched('toast', type: 'success')
        ->assertSet('form.name', '')
        ->assertSee('Ada');

    $contact = ClientContact::query()->sole();

    expect($contact->client_id)->toBe($this->client->id)
        ->and($contact->phone)->toBeNull()
        ->and($contact->phone_ext)->toBeNull();
});

it('does not duplicate or update when saving a new contact twice', function () {
    $component = ($this->manager)()->set(validContact())->call('save');

    $component->set(validContact(['form.name' => 'Second', 'form.email' => 'second@example.com']))->call('save');

    expect(ClientContact::query()->pluck('name')->sort()->values()->all())->toBe(['Ada', 'Second']);
});

it('validates contacts', function (array $overrides, string $error) {
    ($this->manager)()
        ->set(validContact($overrides))
        ->call('save')
        ->assertHasErrors([$error])
        ->assertNotDispatched('close-dialog');

    expect(ClientContact::query()->count())->toBe(0);
})->with([
    'missing name' => [['form.name' => ''], 'form.name'],
    'missing title' => [['form.title' => ''], 'form.title'],
    'missing email' => [['form.email' => ''], 'form.email'],
    'bad email' => [['form.email' => 'nope'], 'form.email'],
    'long phone' => [['form.phone' => str_repeat('1', 31)], 'form.phone'],
    'long extension' => [['form.phone_ext' => '123456'], 'form.phone_ext'],
]);

it('edits a contact', function () {
    $contact = ClientContact::factory()->for($this->client)->create(['name' => 'Old', 'phone' => null]);

    ($this->manager)()
        ->call('edit', $contact->uuid)
        ->assertDispatched('open-dialog', name: 'contact-form')
        ->assertSet('editingUuid', $contact->uuid)
        ->assertSet('form.name', 'Old')
        ->assertSet('form.phone', '')
        ->set('form.name', 'New')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('editingUuid', null);

    expect($contact->fresh()->name)->toBe('New')
        ->and(ClientContact::query()->count())->toBe(1);
});

it('forgets the edit when the dialog closes', function () {
    $contact = ClientContact::factory()->for($this->client)->create();

    ($this->manager)()
        ->call('edit', $contact->uuid)
        ->dispatch('dialog-closed', name: 'contact-form')
        ->assertSet('editingUuid', null)
        ->assertSet('form.name', '');
});

it('confirms before deleting', function () {
    $contact = ClientContact::factory()->for($this->client)->create(['name' => 'Ada']);

    $component = ($this->manager)()
        ->call('confirmDelete', $contact->uuid)
        ->assertDispatched('open-dialog', name: 'contact-delete')
        ->assertSee('This permanently deletes Ada.');

    expect($contact->fresh())->not->toBeNull();

    $component->call('delete')->assertDispatched('toast', type: 'success');

    expect($contact->fresh())->toBeNull();
});

it('does nothing when deleting without confirming', function () {
    ClientContact::factory()->for($this->client)->create();

    ($this->manager)()->call('delete');

    expect(ClientContact::query()->count())->toBe(1);
});

it('locks record identifiers', function (string $property) {
    ($this->manager)()->set($property, 1);
})->with(['editingUuid', 'deletingUuid'])->throws('Cannot update locked property');

describe('another client\'s contact', function () {
    beforeEach(function () {
        $this->foreign = ClientContact::factory()->create(['name' => 'Foreign']);
    });

    it('cannot be edited', function () {
        ($this->manager)()->call('edit', $this->foreign->uuid)->assertNotFound();
    });

    it('cannot be queued for deletion', function () {
        ($this->manager)()->call('confirmDelete', $this->foreign->uuid)->assertNotFound();

        expect($this->foreign->fresh())->not->toBeNull();
    });
});

describe('another user', function () {
    beforeEach(function () {
        $this->contact = ClientContact::factory()->for($this->client)->create(['name' => 'Ada']);
    });

    it('cannot mount the manager', function () {
        $this->actingAs(User::factory()->create());

        ($this->manager)()->assertNotFound();
    });

    it('cannot act on a component they obtained', function (Closure $action) {
        $component = ($this->manager)();

        $this->actingAs(User::factory()->create());

        $action($component)->assertForbidden();

        expect($this->contact->fresh()->name)->toBe('Ada');
    })->with([
        'edit' => fn ($component) => $component->call('edit', (string) Str::uuid7()),
        'save' => fn ($component) => $component->call('save'),
        'confirm delete' => fn ($component) => $component->call('confirmDelete', (string) Str::uuid7()),
        'delete' => fn ($component) => $component->call('delete'),
        'export' => fn ($component) => $component->call('export', 'csv'),
    ]);
});
