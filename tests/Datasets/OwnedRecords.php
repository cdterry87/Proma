<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Models\UserNotification;

/**
 * Every record type that a user owns directly. Each case is a closure that
 * creates a record for the given owner.
 */
dataset('owned records', [
    'client' => fn (User $owner) => Client::factory()->for($owner)->create(),
    'project' => fn (User $owner) => Project::factory()->for($owner)->create(),
    'issue' => fn (User $owner) => Issue::factory()->for($owner)->create(),
    'notification' => fn (User $owner) => UserNotification::factory()->for($owner)->create(),
]);
