<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
use Database\Seeders\DemotUserSeeder;
use Illuminate\Bus\Queueable;
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
        $users = User::query()
            ->select('id')
            ->where('demo', true)
            ->get()
            ->pluck('id')
            ->toArray();

        // Delete all clients of demo users
        Client::query()
            ->whereIn('user_id', $users)
            ->delete();

        // Delete all projects of demo users
        Project::query()
            ->whereIn('user_id', $users)
            ->delete();

        // Delete all issues of demo users
        Issue::query()
            ->whereIn('user_id', $users)
            ->delete();

        // Run DemoUserSeeder to repopulate data
        $seeder = new DemoUserSeeder();
        $seeder->run();
    }
}
