<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Visitors share demo accounts, so changing the email or password of one
     * would lock everyone else out.
     */
    public function changeCredentials(User $user): bool
    {
        return ! $user->is_demo;
    }

    /**
     * Demo accounts never receive email.
     */
    public function changeEmailNotifications(User $user): bool
    {
        return ! $user->is_demo;
    }
}
