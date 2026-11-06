<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Concerns\RateLimitsAttempts;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
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
