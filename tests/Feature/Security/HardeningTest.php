<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Livewire\Settings;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/**
 * The snapshot of the named component, as the page rendered it.
 */
function componentSnapshot(string $html, string $name): string
{
    preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);

    foreach ($matches[1] as $encoded) {
        $snapshot = html_entity_decode($encoded);

        if (json_decode($snapshot, true)['memo']['name'] === $name) {
            return $snapshot;
        }
    }

    throw new RuntimeException("No snapshot for {$name}.");
}

it('ends Livewire actions for a session that a password change ended', function () {
    $user = User::factory()->create();
    Client::factory()->for($user)->create(['name' => 'Secret Co']);

    $snapshot = componentSnapshot($this->actingAs($user)->get('/clients')->getContent(), 'clients');
    $update = function () use ($snapshot) {
        // A real server starts each request fresh. Tests share one process.
        app('livewire')->flushState();

        return $this->withHeaders(['X-Livewire' => '1'])->postJson(app('livewire')->getUpdateUri(), [
            '_token' => csrf_token(),
            'components' => [['snapshot' => $snapshot, 'updates' => ['search' => 'Secret'], 'calls' => []]],
        ]);
    };

    $update()->assertOk()->assertSee('Secret Co');

    $user->forceFill(['password' => Hash::make('a-new-password')])->save();

    $update()->assertUnauthorized()->assertDontSee('Secret Co');
    $this->assertGuest();
});

it('throttles Livewire requests', function () {
    $route = collect(Route::getRoutes()->getRoutes())->first(fn ($route) => str_ends_with((string) $route->getName(), 'livewire.update'));

    expect($route->gatherMiddleware())->toContain('web', 'throttle:livewire');
});

it('limits sign in attempts from one address across many emails', function () {
    foreach (range(1, 20) as $attempt) {
        Livewire::test(Login::class)
            ->set('email', "person{$attempt}@example.com")
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email' => 'Email or password is incorrect.']);
    }

    User::factory()->create(['email' => 'real@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'real@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email')
        ->assertSee('Too many attempts');

    $this->assertGuest();
});

it('limits guesses of the current password in settings', function () {
    $user = User::factory()->create();
    $component = Livewire::actingAs($user)->test(Settings::class)->call('edit', 'password');

    foreach (range(1, 5) as $attempt) {
        $component->set('current_password', 'wrong-password')->call('savePassword')->assertHasErrors('current_password');
    }

    $component
        ->set('current_password', 'password')
        ->set('password', 'a-new-password')
        ->set('password_confirmation', 'a-new-password')
        ->call('savePassword')
        ->assertHasErrors('current_password')
        ->assertSee('Too many attempts');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

it('sends hardening headers', function () {
    $this->get('/login')
        ->assertHeader('Permissions-Policy')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
        ->assertHeaderMissing('Strict-Transport-Security')
        ->assertHeaderMissing('X-Powered-By');

    $this->get('https://localhost/login')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

it('serves nothing from storage', function () {
    expect(Route::has('storage.local'))->toBeFalse();
});
