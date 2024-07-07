<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ProjectNotificationSent;
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
            ->whereDate('due_date', now()->addDays(10)->toDateString())
            ->whereNull('completed_date')
            ->whereDoesntHave('dueNotificationSent')
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Due Reminder',
                'message' => 'Your project is due in 5 days: ' . $project->name,
            ]);

            ProjectNotificationSent::create([
                'user_id' => $project->user_id,
                'project_id' => $project->id,
                'due' => true,
                'overdue' => false,
                'completed' => false,
            ]);
        }
    }

    protected function handleProjectOverdueNotifications()
    {
        $projects = Project::query()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNull('completed_date')
            ->whereDoesntHave('overdueNotificationSent')
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Overdue',
                'message' => 'Your project is overdue: ' . $project->name,
            ]);

            ProjectNotificationSent::create([
                'user_id' => $project->user_id,
                'project_id' => $project->id,
                'due' => false,
                'overdue' => true,
                'completed' => false,
            ]);
        }
    }

    protected function handleCompletedProjectNotifications()
    {
        $projects = Project::query()
            ->where('completed_date', '<=', now()->toDateString())
            ->whereDoesntHave('completeNotificationSent')
            ->get();

        foreach ($projects as $project) {
            UserNotification::create([
                'user_id' => $project->user_id,
                'subject' => 'Project Completed',
                'message' => 'Your project has been completed: ' . $project->name,
            ]);

            ProjectNotificationSent::create([
                'user_id' => $project->user_id,
                'project_id' => $project->id,
                'due' => false,
                'overdue' => false,
                'completed' => true,
            ]);
        }
    }
}
