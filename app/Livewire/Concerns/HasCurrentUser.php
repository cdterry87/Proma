<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasCurrentUser
{
    private function currentUser(): User
    {
        /** @var User */
        return Auth::user();
    }
}
