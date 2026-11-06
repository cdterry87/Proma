<?php

declare(strict_types=1);

use App\Livewire\Styleguide;
use Livewire\Livewire;

it('renders outside production', function () {
    $this->get('/styleguide')
        ->assertOk()
        ->assertSeeLivewire(Styleguide::class)
        ->assertSee('Style Reference');
});

it('is hidden in production', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/styleguide')->assertNotFound();
});

it('shows validation errors linked to their fields', function () {
    Livewire::test(Styleguide::class)
        ->call('save')
        ->assertHasErrors(['name', 'email', 'description', 'priority'])
        ->assertNotDispatched('close-dialog')
        ->assertSeeHtml('aria-invalid="true"')
        ->assertSeeHtml('aria-describedby="dialog-name-error"');
});

it('closes the dialog and confirms after a valid save', function () {
    Livewire::test(Styleguide::class)
        ->set('name', 'A')
        ->set('email', 'a@example.com')
        ->set('description', 'B')
        ->set('priority', '2')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-dialog', name: 'sample-form')
        ->assertDispatched('toast', type: 'success');
});

it('sorts and paginates the sample table', function () {
    Livewire::test(Styleguide::class)
        ->assertSee('Sample record 01')
        ->call('sortBy', 'name')
        ->assertSee('Sample record 42')
        ->assertDontSee('Sample record 01')
        ->assertSeeHtml('aria-sort="descending"')
        ->call('gotoPage', 2)
        ->assertSee('Sample record 37');
});
