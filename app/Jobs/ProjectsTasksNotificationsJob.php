<?php

namespace App\Jobs;

use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use App\Models\UserNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProjectsTasksNotificationsJob implements ShouldQueue
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
        $this->handleProjectTaskDueReminderNotifications();
        $this->handleProjectTaskOverdueNotifications();
    }

    protected function handleProjectTaskDueReminderNotifications()
    {
        $tasks = ProjectTask::query()
            ->whereDate('due_date', now()->addDays(5)->toDateString())
            ->whereNull('completed_date')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $task->user_id,
                'subject' => 'Project Task Due Reminder',
                'message' => 'Your project\'s task is due in 5 days: ' . $task->name . ' in project ' . $task->project->name,
            ]);
        }
    }

    protected function handleProjectTaskOverdueNotifications()
    {
        $tasks = ProjectTask::query()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNull('completed_date')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $task->user_id,
                'subject' => 'Project Task Overdue',
                'message' => 'Your project\'s task is overdue: ' . $task->name . ' in project ' . $task->project->name,
            ]);
        }
    }
}
