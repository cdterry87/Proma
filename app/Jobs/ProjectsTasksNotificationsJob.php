<?php

namespace App\Jobs;

use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use App\Models\UserNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ProjectTaskNotificationSent;
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
        $this->handleProjectTaskCompletedNotifications();
    }

    protected function handleProjectTaskDueReminderNotifications()
    {
        $tasks = ProjectTask::query()
            ->whereDate('due_date', now()->addDays(10)->toDateString())
            ->whereNull('completed_date')
            ->whereDoesntHave('dueNotificationSent')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $task->project->user_id,
                'subject' => 'Project Task Due Reminder',
                'message' => 'Your project\'s task is due in 5 days: ' . $task->name . ' in project ' . $task->project->name,
            ]);

            ProjectTaskNotificationSent::create([
                'user_id' => $task->project->user_id,
                'project_task_id' => $task->id,
                'due' => true,
                'overdue' => false,
                'completed' => false,
            ]);
        }
    }

    protected function handleProjectTaskOverdueNotifications()
    {
        $tasks = ProjectTask::query()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNull('completed_date')
            ->whereDoesntHave('overdueNotificationSent')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $task->project->user_id,
                'subject' => 'Project Task Overdue',
                'message' => 'Your project\'s task is overdue: ' . $task->name . ' in project ' . $task->project->name,
            ]);

            ProjectTaskNotificationSent::create([
                'user_id' => $task->project->user_id,
                'project_task_id' => $task->id,
                'due' => false,
                'overdue' => true,
                'completed' => false,
            ]);
        }
    }

    protected function handleProjectTaskCompletedNotifications()
    {
        $tasks = ProjectTask::query()
            ->where('completed_date', '<=', now()->toDateString())
            ->whereDoesntHave('completeNotificationSent')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $task->project->user_id,
                'subject' => 'Project Task Completed',
                'message' => 'Your project\'s task is completed: ' . $task->name . ' in project ' . $task->project->name,
            ]);

            ProjectTaskNotificationSent::create([
                'user_id' => $task->project->user_id,
                'project_task_id' => $task->id,
                'due' => false,
                'overdue' => false,
                'completed' => true,
            ]);
        }
    }
}
