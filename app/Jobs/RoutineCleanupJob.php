<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\IssueNotificationSent;
use App\Models\Project;
use App\Models\ProjectNotificationSent;
use App\Models\ProjectTaskNotificationSent;
use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RoutineCleanupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all demo user ids
        $demoUsers = User::query()
            ->select('id')
            ->where('demo', true)
            ->get()
            ->pluck('id')
            ->toArray();

        // Delete all clients of demo users
        Client::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        // Delete all projects of demo users
        Project::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        // Delete all issues of demo users
        Issue::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        // Delete all user notifications and sent history of demo users
        UserNotification::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        IssueNotificationSent::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        ProjectNotificationSent::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        ProjectTaskNotificationSent::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        // Run DemoUserSeeder to repopulate data
        $seeder = new DemoUserSeeder();
        $seeder->run();
    }
}
