<?php

declare(strict_types=1);

use App\Livewire\Clients\Index;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('requires authentication', function () {
    auth()->logout();

    $this->get('/clients')->assertRedirect('/login');
});

it('lists only the user\'s clients', function () {
    Client::factory()->for($this->user)->create(['name' => 'Mine']);
    Client::factory()->create(['name' => 'Theirs']);

    $this->get('/clients')
        ->assertOk()
        ->assertSeeLivewire(Index::class)
        ->assertSee('Mine')
        ->assertDontSee('Theirs');
});

it('counts only incomplete projects and unresolved issues', function () {
    $client = Client::factory()->for($this->user)->create();
    Project::factory()->forClient($client)->count(2)->create();
    Project::factory()->forClient($client)->completed()->create();
    Issue::factory()->forClient($client)->create();
    Issue::factory()->forClient($client)->resolved()->create();
    ClientContact::factory()->for($client)->count(3)->create();

    $row = Livewire::test(Index::class)->viewData('rows')->first();

    expect($row->incomplete_projects_count)->toBe(2)
        ->and($row->unresolved_issues_count)->toBe(1)
        ->and($row->contacts_count)->toBe(3);
});

it('escapes stored values [D-4]', function () {
    Client::factory()->for($this->user)->create(['name' => '<b>Bold</b>', 'description' => '<script>x</script>']);

    Livewire::test(Index::class)
        ->assertDontSeeHtml('<b>Bold</b>')
        ->assertDontSeeHtml('<script>x</script>')
        ->assertSee('<b>Bold</b>');
});

it('creates a client owned by the user, then closes and resets the form', function () {
    Livewire::test(Index::class)
        ->set('form.name', 'Acme')
        ->set('form.description', 'A company')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-dialog', name: 'client-form')
        ->assertDispatched('toast', type: 'success')
        ->assertSet('form.name', '')
        ->assertSet('form.active', true)
        ->assertSee('Acme');

    $client = Client::query()->sole();

    expect($client->user_id)->toBe($this->user->id)
        ->and($client->active)->toBeTrue();
});

it('validates the form and keeps the dialog open', function (array $input, string $error) {
    Livewire::test(Index::class)
        ->set(['form.name' => 'Acme', 'form.description' => 'A company', ...$input])
        ->call('save')
        ->assertHasErrors([$error])
        ->assertNotDispatched('close-dialog')
        ->assertSeeHtml('aria-invalid="true"');

    expect(Client::query()->count())->toBe(0);
})->with([
    'missing name' => [['form.name' => ''], 'form.name'],
    'long name' => [['form.name' => str_repeat('a', 256)], 'form.name'],
    'missing description' => [['form.description' => ''], 'form.description'],
]);

it('discards unsaved input when the dialog closes', function () {
    Livewire::test(Index::class)
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors()
        ->set('form.description', 'Unsaved')
        ->dispatch('dialog-closed', name: 'client-form')
        ->assertHasNoErrors()
        ->assertSet('form.description', '');
});

it('ignores other dialogs closing', function () {
    Livewire::test(Index::class)
        ->set('form.description', 'Kept')
        ->dispatch('dialog-closed', name: 'other')
        ->assertSet('form.description', 'Kept');
});
