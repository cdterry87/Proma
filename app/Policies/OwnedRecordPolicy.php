<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Auth\Access\Response;

/**
 * Viewing someone else's record responds as if it does not exist, so that
 * pages do not disclose which records exist. Actions respond with a 403.
 */
abstract class OwnedRecordPolicy
{
    public function view(User $user, Client|Project|Issue|UserNotification $record): Response
    {
        return $record->isOwnedBy($user) ? Response::allow() : Response::denyAsNotFound();
    }

    public function update(User $user, Client|Project|Issue|UserNotification $record): bool
    {
        return $record->isOwnedBy($user);
    }

    public function delete(User $user, Client|Project|Issue|UserNotification $record): bool
    {
        return $record->isOwnedBy($user);
    }
}
