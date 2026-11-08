<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\RateLimitsAttempts;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Settings')]
class Settings extends Component
{
    use RateLimitsAttempts;

    private const array EDITABLE = ['name', 'email', 'title', 'password'];

    public ?string $editing = null;

    /**
     * The field whose edit button takes focus back when its form closes.
     */
    #[Locked]
    public ?string $lastEdited = null;

    public string $name = '';

    public string $email = '';

    public string $title = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $emailNotifications = true;

    public function mount(): void
    {
        $this->fillFromUser();
    }

    public function edit(string $field): void
    {
        abort_unless(in_array($field, self::EDITABLE, true), 404);

        if (in_array($field, ['email', 'password'], true)) {
            Gate::authorize('changeCredentials', $this->user());
        }

        $this->cancel();
        $this->editing = $field;
    }

    public function cancel(): void
    {
        $this->resetValidation();
        $this->fillFromUser();
        $this->lastEdited = $this->editing;
        $this->editing = null;
    }

    public function saveName(): void
    {
        $validated = $this->validate(['name' => ['required', 'string', 'max:255']]);

        $this->saveProfile($validated, 'Name updated.');
    }

    public function saveTitle(): void
    {
        $validated = $this->validate(['title' => ['nullable', 'string', 'max:255']]);

        $this->saveProfile(['title' => $validated['title'] ?: null], 'Title updated.');
    }

    public function saveEmail(): void
    {
        $user = $this->user();

        Gate::authorize('changeCredentials', $user);

        $this->email = User::normalizeEmail($this->email);

        $validated = $this->validateWithCurrentPassword([
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
        ]);

        $this->saveProfile(['email' => $validated['email']], 'Email updated.');
    }

    public function savePassword(): void
    {
        $user = $this->user();

        Gate::authorize('changeCredentials', $user);

        $validated = $this->validateWithCurrentPassword([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user->update(['password' => $validated['password']]);

        // The new hash ends every other session through AuthenticateSession.
        // These calls refresh this browser's remember cookie and session hash
        // so that it stays signed in.
        Auth::logoutOtherDevices($validated['password']);
        Auth::login($user);

        $this->cancel();
        $this->dispatch('toast', type: 'success', message: 'Password updated.');
    }

    public function updatedEmailNotifications(bool $value): void
    {
        $user = $this->user();

        Gate::authorize('changeEmailNotifications', $user);

        $user->update(['email_notifications' => $value]);

        $this->dispatch('toast', type: 'success', message: $value ? 'Email notifications on.' : 'Email notifications off.');
    }

    public function render(): View
    {
        return view('livewire.settings', ['user' => $this->user()]);
    }

    /**
     * Wrong guesses are limited, so a stolen session cannot be used to find
     * the password and lock the owner out.
     *
     * @param  array<string, list<mixed>>  $rules
     * @return array<string, mixed>
     */
    private function validateWithCurrentPassword(array $rules): array
    {
        $key = 'current-password|'.$this->user()->getAuthIdentifier();

        $this->ensureIsNotRateLimited($key, 'current_password');

        try {
            return $this->validate([...$rules, 'current_password' => ['required', 'current_password']]);
        } catch (ValidationException $exception) {
            if (array_key_exists('current_password', $exception->errors()) && $this->current_password !== '') {
                RateLimiter::hit($key);
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function saveProfile(array $attributes, string $message): void
    {
        $this->user()->update($attributes);

        $this->cancel();
        $this->dispatch('profile-updated');
        $this->dispatch('toast', type: 'success', message: $message);
    }

    private function fillFromUser(): void
    {
        $user = $this->user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->title = $user->title ?? '';
        $this->emailNotifications = $user->email_notifications;
        $this->reset('current_password', 'password', 'password_confirmation');
    }

    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }
}
