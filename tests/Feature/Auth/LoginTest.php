<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('redirects guests to the login page and back to the intended page', function () {
    $this->get('/settings')->assertRedirect('/login');

    $user = User::factory()->create(['email' => 'a@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'a@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect('/settings');

    expect(Auth::id())->toBe($user->id);
});

it('signs in regardless of email case', function () {
    User::factory()->create(['email' => 'a@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'A@Example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect('/');
});

it('remembers the user when asked', function () {
    $user = User::factory()->create(['remember_token' => null]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login');

    expect($user->fresh()->remember_token)->not->toBeNull();
});

it('uses one failure message for a wrong password and an unknown email', function (string $email) {
    User::factory()->create(['email' => 'a@example.com']);

    Livewire::test(Login::class)
        ->set('email', $email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertSee('Email or password is incorrect.')
        ->assertSet('password', '');

    expect(Auth::check())->toBeFalse();
})->with(['a@example.com', 'nobody@example.com']);

it('validates the login form', function () {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors(['email' => 'required', 'password' => 'required']);
});

it('blocks a sixth attempt even with the correct password [D-5]', function () {
    $user = User::factory()->create();
    $component = Livewire::test(Login::class)->set('email', $user->email);

    foreach (range(1, 5) as $attempt) {
        $component->set('password', 'wrong-password')->call('login');
    }

    $component->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertSee('Too many attempts.');

    expect(Auth::check())->toBeFalse();
});

it('clears the attempt count after a successful login', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect('/');

    expect(RateLimiter::attempts('login|'.$user->email.'|127.0.0.1'))->toBe(0);
});

it('redirects authenticated visitors away from guest pages [D-23]', function (string $url) {
    $this->actingAs(User::factory()->create())->get($url)->assertRedirect('/');
})->with(['/login', '/forgot-password', '/reset-password/token']);

describe('the demo login', function () {
    beforeEach(fn () => config(['proma.demo' => ['enabled' => true, 'email' => 'demo@example.com', 'password' => 'demo-secret']]));

    it('offers a one-click sign in without showing the credentials', function () {
        $this->get('/login')
            ->assertSee('Try the demo')
            ->assertSee('the demo data resets daily')
            ->assertSee('Sign in as the demo user')
            ->assertDontSee('demo@example.com')
            ->assertDontSee('demo-secret');
    });

    it('is hidden while the demo is off or not configured', function (array $demo) {
        config(['proma.demo' => $demo]);

        $this->get('/login')->assertDontSee('Try the demo');
    })->with([
        'off' => [['enabled' => false, 'email' => 'demo@example.com', 'password' => 'demo-secret']],
        'no email' => [['enabled' => true, 'email' => null, 'password' => 'demo-secret']],
        'no password' => [['enabled' => true, 'email' => 'demo@example.com', 'password' => null]],
    ]);

    it('signs in as the demo user and ignores the intended page', function () {
        $demo = User::factory()->create(['email' => 'demo@example.com', 'is_demo' => true]);
        session()->put('url.intended', '/clients/gone');

        Livewire::test(Login::class)
            ->call('loginAsDemo')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($demo);
        expect(session()->has('url.intended'))->toBeFalse();
    });

    it('never signs in a regular account that uses the demo email', function () {
        User::factory()->create(['email' => 'demo@example.com', 'is_demo' => false]);

        Livewire::test(Login::class)
            ->call('loginAsDemo')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    });

    it('refuses while the demo is off', function () {
        config(['proma.demo.enabled' => false]);
        User::factory()->create(['email' => 'demo@example.com', 'is_demo' => true]);

        Livewire::test(Login::class)->call('loginAsDemo')->assertNotFound();

        $this->assertGuest();
    });
});

it('logs out and invalidates the session', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/login')
        ->assertSessionHas('toast.type', 'success');

    $this->assertGuest();
});

it('does not allow guests to log out', function () {
    $this->post('/logout')->assertRedirect('/login');
});
