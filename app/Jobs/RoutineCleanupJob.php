<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
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
        // Get all guest user ids
        $users = User::query()
            ->select('id')
            ->where('guest', true)
            ->get()
            ->pluck('id')
            ->toArray();

        // Delete all clients of guest users
        Client::query()
            ->whereIn('user_id', $users)
            ->delete();

        // Delete all projects of guest users
        Project::query()
            ->whereIn('user_id', $users)
            ->delete();

        // Delete all issues of guest users
        Issue::query()
            ->whereIn('user_id', $users)
            ->delete();
    }
}
