<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Concerns\RateLimitsAttempts;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Reset password')]
class ResetPassword extends Component
{
    use RateLimitsAttempts;

    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email')->toString();
    }

    public function resetPassword(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
        ]);

        $key = 'reset-password|'.request()->ip();

        $this->ensureIsNotRateLimited($key, 'email');
        RateLimiter::hit($key);

        $status = Password::reset(
            [
                'email' => User::normalizeEmail($this->email),
                'is_demo' => false,
                'password' => $this->password,
                'token' => $this->token,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        $this->reset('password', 'password_confirmation');

        // One message for every failure, so this form cannot be used to find
        // out whether an email is registered.
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => 'This reset link is invalid or has expired.']);
        }

        session()->flash('toast', ['type' => 'success', 'message' => 'Your password has been reset. You can now sign in.']);

        $this->redirectRoute('login');
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password');
    }
}
