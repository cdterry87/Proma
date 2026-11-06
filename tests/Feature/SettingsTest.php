<?php

declare(strict_types=1);

use App\Livewire\Settings;
use App\Livewire\SidebarUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Ada', 'title' => 'Engineer']);
    $this->actingAs($this->user);
});

it('requires authentication', function () {
    Auth::logout();

    $this->get('/settings')->assertRedirect('/login');
});

it('renders the current values', function () {
    $this->get('/settings')
        ->assertOk()
        ->assertSeeLivewire(Settings::class)
        ->assertSee('Ada')
        ->assertSee($this->user->email);
});

it('updates the name, closes the form, and refreshes the sidebar [D-24]', function () {
    Livewire::test(Settings::class)
        ->call('edit', 'name')
        ->assertSet('editing', 'name')
        ->set('name', 'Al')
        ->call('saveName')
        ->assertHasNoErrors()
        ->assertSet('editing', null)
        ->assertDispatched('profile-updated')
        ->assertDispatched('toast', type: 'success');

    expect($this->user->fresh()->name)->toBe('Al');

    Auth::setUser($this->user->fresh());

    Livewire::test(SidebarUser::class)->dispatch('profile-updated')->assertSee('Al');
});

it('validates the name', function (string $value) {
    Livewire::test(Settings::class)->set('name', $value)->call('saveName')->assertHasErrors(['name']);
})->with(['', str_repeat('a', 256)]);

it('updates and clears the title', function () {
    Livewire::test(Settings::class)->set('title', 'CTO')->call('saveTitle')->assertHasNoErrors();
    expect($this->user->fresh()->title)->toBe('CTO');

    Livewire::test(Settings::class)->set('title', '')->call('saveTitle')->assertHasNoErrors();
    expect($this->user->fresh()->title)->toBeNull();
});

it('updates the email with the current password', function () {
    Livewire::test(Settings::class)
        ->set('email', ' New@Example.com ')
        ->set('current_password', 'password')
        ->call('saveEmail')
        ->assertHasNoErrors()
        ->assertSet('current_password', '');

    expect($this->user->fresh()->email)->toBe('new@example.com');
});

it('rejects an email change without the current password [D-6]', function () {
    $email = $this->user->email;

    Livewire::test(Settings::class)
        ->set('email', 'new@example.com')
        ->set('current_password', 'wrong-password')
        ->call('saveEmail')
        ->assertHasErrors(['current_password']);

    expect($this->user->fresh()->email)->toBe($email);
});

it('rejects an email that is taken, whatever its case', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(Settings::class)
        ->set('email', 'TAKEN@example.com')
        ->set('current_password', 'password')
        ->call('saveEmail')
        ->assertHasErrors(['email' => 'unique']);
});

it('updates the password and keeps this session signed in', function () {
    Livewire::test(Settings::class)
        ->set('current_password', 'password')
        ->set('password', 'a-new-password')
        ->set('password_confirmation', 'a-new-password')
        ->call('savePassword')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('editing', null);

    $user = $this->user->fresh();

    expect(Hash::check('a-new-password', $user->password))->toBeTrue()
        ->and(session('password_hash_web'))->toBe(Auth::guard()->hashPasswordForCookie($user->password));

    $this->get('/settings')->assertOk();
});

it('signs out a session holding the old password hash', function () {
    $this->withSession(['password_hash_web' => Auth::guard()->hashPasswordForCookie($this->user->password)]);
    $this->get('/settings')->assertOk();

    $this->user->update(['password' => 'a-new-password']);

    $this->get('/settings')->assertRedirect('/login');
});

it('rejects a password change with bad input [D-6]', function (array $input, string $error) {
    Livewire::test(Settings::class)
        ->set($input)
        ->call('savePassword')
        ->assertHasErrors([$error]);

    expect(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
})->with([
    'wrong current password' => [['current_password' => 'wrong', 'password' => 'a-new-password', 'password_confirmation' => 'a-new-password'], 'current_password'],
    'too short' => [['current_password' => 'password', 'password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'not confirmed' => [['current_password' => 'password', 'password' => 'a-new-password', 'password_confirmation' => 'different'], 'password'],
]);

it('restores values on cancel', function () {
    Livewire::test(Settings::class)
        ->call('edit', 'name')
        ->set('name', 'Changed')
        ->call('cancel')
        ->assertSet('name', 'Ada')
        ->assertSet('editing', null);
});

it('moves focus into a form when it opens and back to its button when it closes', function () {
    $component = Livewire::test(Settings::class)
        ->assertDontSeeHtml('data-focus-target')
        ->call('edit', 'title');

    expect(substr_count($component->html(), 'data-focus-target='))->toBe(1);
    $component->assertSeeHtml('data-focus-target="data-focus-target"');

    $component->call('cancel')->assertSet('lastEdited', 'title');

    expect(substr_count($component->html(), 'data-focus-target='))->toBe(1);
});

it('rejects an unknown field', function () {
    Livewire::test(Settings::class)->call('edit', 'is_demo')->assertNotFound();
});

it('updates the email notification preference', function () {
    Livewire::test(Settings::class)
        ->set('emailNotifications', false)
        ->assertDispatched('toast', type: 'success');

    expect($this->user->fresh()->email_notifications)->toBeFalse();
});

describe('demo users', function () {
    beforeEach(function () {
        $this->user = User::factory()->demo()->create(['email' => 'demo@example.com']);
        $this->actingAs($this->user);
    });

    it('cannot open the email or password forms', function (string $field) {
        Livewire::test(Settings::class)->call('edit', $field)->assertForbidden();
    })->with(['email', 'password']);

    it('cannot change their email [D-9]', function () {
        Livewire::test(Settings::class)
            ->set('email', 'new@example.com')
            ->set('current_password', 'password')
            ->call('saveEmail')
            ->assertForbidden();

        expect($this->user->fresh()->email)->toBe('demo@example.com');
    });

    it('cannot change their password [D-9]', function () {
        Livewire::test(Settings::class)
            ->set('current_password', 'password')
            ->set('password', 'a-new-password')
            ->set('password_confirmation', 'a-new-password')
            ->call('savePassword')
            ->assertForbidden();

        expect(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
    });

    it('cannot turn on email notifications', function () {
        Livewire::test(Settings::class)->set('emailNotifications', true)->assertForbidden();

        expect($this->user->fresh()->email_notifications)->toBeFalse();
    });

    it('can change their name', function () {
        Livewire::test(Settings::class)->set('name', 'Visitor')->call('saveName')->assertHasNoErrors();

        expect($this->user->fresh()->name)->toBe('Visitor');
    });

    it('sees no edit controls for credentials', function () {
        Livewire::test(Settings::class)
            ->assertDontSeeHtml("edit('email')")
            ->assertDontSeeHtml("edit('password')")
            ->assertSee('Demo accounts cannot change');
    });
});
