<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Models\User;
use App\Support\Turnstile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

beforeEach(function () {
    Http::preventStrayRequests();
    config([
        'proma.demo' => ['enabled' => true, 'email' => 'demo@example.com', 'password' => 'demo-secret'],
        'services.turnstile' => ['site_key' => 'site-key', 'secret_key' => 'secret-key'],
    ]);
    $this->demo = User::factory()->create(['email' => 'demo@example.com', 'is_demo' => true]);
});

it('signs in as the demo user once Cloudflare accepts the token', function () {
    Http::fake([VERIFY_URL => Http::response(['success' => true])]);

    Livewire::test(Login::class)
        ->call('loginAsDemo', 'good-token')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->demo);
    Http::assertSent(fn (Request $request) => $request->url() === VERIFY_URL
        && $request['secret'] === 'secret-key'
        && $request['response'] === 'good-token'
        && $request['remoteip'] === '127.0.0.1');
});

it('refuses the demo sign in when Cloudflare rejects the token', function () {
    Http::fake([VERIFY_URL => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

    Livewire::test(Login::class)
        ->call('loginAsDemo', 'bad-token')
        ->assertNoRedirect()
        ->assertDispatched('toast', type: 'error', message: 'We could not verify that you are a person. Try again.');

    $this->assertGuest();
});

it('refuses the demo sign in without asking Cloudflare when there is no usable token', function (string $token) {
    Http::fake();

    Livewire::test(Login::class)->call('loginAsDemo', $token)->assertNoRedirect();

    $this->assertGuest();
    Http::assertNothingSent();
})->with(['missing' => '', 'too long' => str_repeat('a', 2049)]);

it('refuses the demo sign in when Cloudflare cannot be reached', function () {
    Http::fake(fn () => throw new ConnectionException('Timed out.'));

    Livewire::test(Login::class)->call('loginAsDemo', 'token')->assertNoRedirect();

    $this->assertGuest();
});

it('refuses the demo sign in when Cloudflare answers with an error', function () {
    Http::fake([VERIFY_URL => Http::response(['success' => true], 500)]);

    Livewire::test(Login::class)->call('loginAsDemo', 'token')->assertNoRedirect();

    $this->assertGuest();
});

it('renders the widget and allows Cloudflare in the policy', function () {
    $response = $this->get('/login')
        ->assertSee('x-data="demoLogin"', false)
        ->assertSee('data-sitekey="site-key"', false)
        ->assertDontSee('secret-key');

    expect($response->headers->get('Content-Security-Policy'))
        ->toContain("script-src 'self' 'nonce-")
        ->toContain(Turnstile::SCRIPT_ORIGIN)
        ->toContain("frame-src 'self' ".Turnstile::SCRIPT_ORIGIN);
});

it('skips the check and leaves Cloudflare out of the policy without both keys', function (array $keys) {
    config(['services.turnstile' => $keys]);
    Http::fake();

    $response = $this->get('/login')->assertDontSee('demoLogin');
    expect($response->headers->get('Content-Security-Policy'))->not->toContain('cloudflare');

    Livewire::test(Login::class)->call('loginAsDemo')->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->demo);
    Http::assertNothingSent();
})->with([
    'no keys' => [['site_key' => null, 'secret_key' => null]],
    'no secret' => [['site_key' => 'site-key', 'secret_key' => null]],
]);
