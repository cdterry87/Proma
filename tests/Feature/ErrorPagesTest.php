<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('renders custom error pages with a security policy', function (int $status, string $title) {
    config(['app.debug' => false]);
    Route::get('/_test/error', fn () => abort($status));

    $response = $this->get('/_test/error');

    $response->assertStatus($status)
        ->assertSee($title)
        ->assertSeeHtml('class="illustration"')
        ->assertDontSeeHtml(' style=');

    expect($response->headers->get('Content-Security-Policy'))->toContain("default-src 'self'");
})->with([
    [403, 'Access denied'],
    [404, 'Page not found'],
    [419, 'Page expired'],
    [503, 'Down for maintenance'],
]);

it('renders the 500 page when debug is off', function () {
    config(['app.debug' => false]);
    Route::get('/_test/error', fn () => throw new RuntimeException('Secret detail'));

    $this->get('/_test/error')
        ->assertServerError()
        ->assertSee('Something went wrong')
        ->assertDontSee('Secret detail');
});

it('covers unknown routes, which run outside the web middleware group', function () {
    $response = $this->get('/no-such-page');

    $response->assertNotFound()
        ->assertSee('Page not found')
        ->assertSee('Return home');

    preg_match("/'nonce-([^']+)'/", $response->headers->get('Content-Security-Policy'), $matches);

    $response->assertSee('nonce="'.$matches[1].'"', false);
});
