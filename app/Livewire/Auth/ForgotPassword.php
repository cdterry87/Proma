<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Concerns\RateLimitsAttempts;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Forgot password')]
class ForgotPassword extends Component
{
    use RateLimitsAttempts;

    public string $email = '';

    public bool $sent = false;

    public function send(): void
    {
        $this->validate(['email' => ['required', 'string', 'email']]);

        $key = 'forgot-password|'.request()->ip();

        $this->ensureIsNotRateLimited($key, 'email');
        RateLimiter::hit($key);

        // The status is ignored so the response never reveals whether the
        // email is registered. Demo accounts are not matched at all.
        Password::sendResetLink(['email' => User::normalizeEmail($this->email), 'is_demo' => false]);

        $this->reset('email');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password');
    }
}
