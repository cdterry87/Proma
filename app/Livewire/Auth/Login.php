<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Concerns\RateLimitsAttempts;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Sign in')]
class Login extends Component
{
    use RateLimitsAttempts;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Whether the login page offers the demo account.
     */
    #[Computed]
    public function demo(): bool
    {
        return config('proma.demo.enabled') === true
            && filled(config('proma.demo.email'))
            && filled(config('proma.demo.password'));
    }

    /**
     * Sign in as the demo account without typing its credentials.
     */
    public function loginAsDemo(): void
    {
        abort_unless($this->demo(), 404);

        $user = User::query()
            ->where('email', User::normalizeEmail((string) config('proma.demo.email')))
            ->where('is_demo', true)
            ->first();

        if ($user === null) {
            throw ValidationException::withMessages(['email' => 'The demo account is not available right now.']);
        }

        Auth::login($user);
        session()->regenerate();

        // The nightly reset removes the records a demo visitor's old links point to.
        session()->forget('url.intended');

        $this->redirect(route('dashboard'));
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = User::normalizeEmail($this->email);
        $key = 'login|'.$email.'|'.request()->ip();

        $this->ensureIsNotRateLimited($key, 'email');

        if (! Auth::attempt(['email' => $email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key);
            $this->reset('password');

            throw ValidationException::withMessages(['email' => 'Email or password is incorrect.']);
        }

        RateLimiter::clear($key);
        session()->regenerate();

        // The nightly reset removes the records a demo visitor's old links point to.
        if (Auth::user()?->is_demo) {
            session()->forget('url.intended');
        }

        $this->redirectIntended(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
