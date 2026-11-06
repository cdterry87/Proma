<?php

declare(strict_types=1);

use App\Jobs\SendIssueNotifications;
use App\Jobs\SendProjectNotifications;
use App\Jobs\SendTaskNotifications;
use Illuminate\Support\Facades\Schedule;

// These jobs do not implement ShouldQueue, so they run inside the scheduler and
// the overlap lock covers the whole run. Only their emails go through the queue.
Schedule::job(new SendIssueNotifications)->dailyAt('02:00')->withoutOverlapping(120)->onOneServer();
Schedule::job(new SendProjectNotifications)->dailyAt('04:00')->withoutOverlapping(120)->onOneServer();
Schedule::job(new SendTaskNotifications)->dailyAt('06:00')->withoutOverlapping(120)->onOneServer();
