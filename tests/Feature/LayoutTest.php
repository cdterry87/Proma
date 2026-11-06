<?php

declare(strict_types=1);

it('renders the application shell with its landmarks', function () {
    $this->get('/styleguide')
        ->assertOk()
        ->assertSeeHtml('class="skip-link"')
        ->assertSeeHtml('aria-label="Main"')
        ->assertSeeHtml('id="main"')
        ->assertSeeHtml('aria-label="Toggle color theme"');
});

it('renders the guest layout', function () {
    $this->get('/')
        ->assertOk()
        ->assertSeeHtml('class="logo guest__logo"');
});

it('shows a flashed toast after a redirect', function () {
    $this->withSession(['toast' => ['type' => 'success', 'message' => 'Saved.']])
        ->get('/')
        ->assertSee('Saved.');
});
