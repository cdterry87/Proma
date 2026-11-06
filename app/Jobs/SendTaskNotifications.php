<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationEvent;
use App\Models\Project;
use App\Models\Task;
use App\Support\DateFormatter;
use App\Support\Notifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

/**
 * Only project tasks generate notifications. Issue tasks do not.
 */
class SendTaskNotifications
{
    use Dispatchable;

    public function handle(Notifier $notifier): void
    {
        $days = (int) config('proma.due_reminder_days');

        $this->projectTasks(NotificationEvent::TaskDue)
            ->dueWithin($days)
            ->chunkById(200, function (Collection $tasks) use ($notifier, $days): void {
                foreach ($tasks as $task) {
                    /** @var Project $project */
                    $project = $task->taskable;

                    $notifier->once(
                        $task,
                        NotificationEvent::TaskDue,
                        $project->user,
                        'Project Task Due Reminder',
                        "The task \"{$task->title}\" on project \"{$project->name}\" is due within {$days} days, on ".DateFormatter::format($task->due_date).'.',
                    );
                }
            });

        $this->projectTasks(NotificationEvent::TaskOverdue)
            ->overdue()
            ->chunkById(200, function (Collection $tasks) use ($notifier): void {
                foreach ($tasks as $task) {
                    /** @var Project $project */
                    $project = $task->taskable;

                    $notifier->once(
                        $task,
                        NotificationEvent::TaskOverdue,
                        $project->user,
                        'Project Task Overdue',
                        "The task \"{$task->title}\" on project \"{$project->name}\" was due on ".DateFormatter::format($task->due_date).' and is not complete.',
                    );
                }
            });
    }

    /**
     * @return Builder<Task>
     */
    private function projectTasks(NotificationEvent $event): Builder
    {
        return Task::query()
            ->where('taskable_type', (new Project)->getMorphClass())
            ->whereDoesntHave('notificationLogs', fn ($query) => $query->where('event', $event))
            ->with('taskable.user');
    }
}
