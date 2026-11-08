<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Livewire actions all share one endpoint, so route throttling cannot limit
 * them individually.
 */
trait RateLimitsAttempts
{
    private const int MAX_ATTEMPTS_PER_MINUTE = 5;

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(string $key, string $field, int $maxAttempts = self::MAX_ATTEMPTS_PER_MINUTE): void
    {
        if (! RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            $field => "Too many attempts. Try again in {$seconds} seconds.",
        ]);
    }
}
