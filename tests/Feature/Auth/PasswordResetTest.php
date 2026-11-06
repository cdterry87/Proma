<?php

declare(strict_types=1);

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('sends a reset link', function () {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(ForgotPassword::class)
        ->set('email', strtoupper($user->email))
        ->call('send')
        ->assertSet('sent', true);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('queues the notification with its token encrypted', function () {
    expect(new ResetPasswordNotification('token'))
        ->toBeInstanceOf(ShouldQueue::class)
        ->toBeInstanceOf(ShouldBeEncrypted::class);
});

it('responds the same whether or not the email is registered', function () {
    Notification::fake();
    $user = User::factory()->create();

    foreach ([$user->email, 'nobody@example.com'] as $email) {
        Livewire::test(ForgotPassword::class)
            ->set('email', $email)
            ->call('send')
            ->assertHasNoErrors()
            ->assertSee('If that email is registered');
    }

    Notification::assertCount(1);
});

it('never sends a reset link to a demo account', function () {
    Notification::fake();
    $user = User::factory()->demo()->create();

    Livewire::test(ForgotPassword::class)
        ->set('email', $user->email)
        ->call('send')
        ->assertSet('sent', true);

    Notification::assertNothingSent();
});

it('throttles reset link requests', function () {
    Notification::fake();

    foreach (range(1, 5) as $attempt) {
        Livewire::test(ForgotPassword::class)->set('email', 'nobody@example.com')->call('send');
    }

    Livewire::test(ForgotPassword::class)
        ->set('email', 'nobody@example.com')
        ->call('send')
        ->assertHasErrors(['email'])
        ->assertSee('Too many attempts.');
});

it('renders the reset page with the email from the link', function () {
    $this->get('/reset-password/some-token?email=a@example.com')
        ->assertOk()
        ->assertSee('Reset password')
        ->assertSee('a@example.com');
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create(['remember_token' => 'old-token']);
    $token = Password::createToken($user);

    Livewire::withQueryParams(['email' => $user->email])
        ->test(ResetPassword::class, ['token' => $token])
        ->assertSet('email', $user->email)
        ->set('password', 'a-new-password')
        ->set('password_confirmation', 'a-new-password')
        ->call('resetPassword')
        ->assertRedirect('/login');

    $user->refresh();

    expect(Hash::check('a-new-password', $user->password))->toBeTrue()
        ->and($user->remember_token)->not->toBe('old-token');
});

it('uses one failure message for a bad token, an unknown email, and a demo account', function (array $setup) {
    $user = User::factory()->create(['email' => 'a@example.com']);
    $demo = User::factory()->demo()->create(['email' => 'demo@example.com']);
    $token = Password::createToken($setup['demo'] ? $demo : $user);

    Livewire::test(ResetPassword::class, ['token' => $setup['token'] ?? $token])
        ->set('email', $setup['email'])
        ->set('password', 'a-new-password')
        ->set('password_confirmation', 'a-new-password')
        ->call('resetPassword')
        ->assertHasErrors(['email'])
        ->assertSee('This reset link is invalid or has expired.');

    expect(Hash::check('a-new-password', $user->fresh()->password))->toBeFalse()
        ->and(Hash::check('a-new-password', $demo->fresh()->password))->toBeFalse();
})->with([
    'bad token' => [['email' => 'a@example.com', 'token' => 'bad-token', 'demo' => false]],
    'unknown email' => [['email' => 'nobody@example.com', 'demo' => false]],
    'demo account' => [['email' => 'demo@example.com', 'demo' => true]],
]);

it('validates the new password', function () {
    Livewire::test(ResetPassword::class, ['token' => 'token'])
        ->set('email', 'a@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'different')
        ->call('resetPassword')
        ->assertHasErrors(['password']);
});

it('locks the token', function () {
    Livewire::test(ResetPassword::class, ['token' => 'token'])->set('token', 'other');
})->throws('Cannot update locked property');
