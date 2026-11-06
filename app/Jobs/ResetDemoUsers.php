<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ResetDemoData;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class ResetDemoUsers
{
    use Dispatchable;

    public function handle(ResetDemoData $reset): void
    {
        User::query()
            ->where('is_demo', true)
            ->lazyById()
            ->each(fn (User $user) => $reset->handle($user));
    }
}
