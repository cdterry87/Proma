<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\UserNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProjectsNotificationsJob implements ShouldQueue
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
        $this->handleProjectDueReminderNotifications();
        $this->handleProjectOverdueNotifications();
        $this->handleCompletedProjectNotifications();
    }

    protected function handleProjectDueReminderNotifications()
    {
        $projects = Project::query()
            ->whereDate('due_date', now()->addDays(5)->toDateString())
            ->whereNull('completed_date')
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Due Reminder',
                'message' => 'Your project is due in 5 days: ' . $project->name,
            ]);
        }
    }

    protected function handleProjectOverdueNotifications()
    {
        $projects = Project::query()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNull('completed_date')
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Overdue',
                'message' => 'Your project is overdue: ' . $project->name,
            ]);
        }
    }

    protected function handleCompletedProjectNotifications()
    {
        $projects = Project::query()
            ->whereDate('completed_date', now()->toDateString())
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Completed',
                'message' => 'Your project has been completed: ' . $project->name,
            ]);
        }
    }
}
