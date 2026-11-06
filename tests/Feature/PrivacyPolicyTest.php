<?php

declare(strict_types=1);

use App\Models\User;

it('uses the guest layout for guests', function () {
    $this->get('/privacy-policy')
        ->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSeeHtml('guest__content--wide')
        ->assertSee('Chase Terry')
        ->assertDontSeeHtml('aria-label="Main"');
});

it('uses the application layout for signed in users', function () {
    $this->actingAs(User::factory()->create())
        ->get('/privacy-policy')
        ->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSeeHtml('aria-label="Main"');
});

it('reflects how the application handles data', function () {
    $this->get('/privacy-policy')
        ->assertSee('notification emails off')
        ->assertSee('Demo accounts never receive email')
        ->assertSee('fonts are served from our own server')
        ->assertSee(config('proma.contact_url'))
        ->assertDontSee('support@proma.com');
});
