<?php

declare(strict_types=1);
use Illuminate\Database\Eloquent\Model;

it('renders the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(config('app.name'));
});

it('enables strict models outside production', function () {
    expect(Model::preventsLazyLoading())->toBeTrue();
});
