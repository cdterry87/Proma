<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationEvent;
use App\Models\Project;
use App\Support\DateFormatter;
use App\Support\Notifier;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class SendProjectNotifications
{
    use Dispatchable;

    public function handle(Notifier $notifier): void
    {
        $days = (int) config('proma.due_reminder_days');

        Project::query()
            ->dueWithin($days)
            ->whereDoesntHave('notificationLogs', fn ($query) => $query->where('event', NotificationEvent::ProjectDue))
            ->with('user')
            ->chunkById(200, function (Collection $projects) use ($notifier, $days): void {
                foreach ($projects as $project) {
                    $notifier->once(
                        $project,
                        NotificationEvent::ProjectDue,
                        $project->user,
                        'Project Due Reminder',
                        "The project \"{$project->name}\" is due within {$days} days, on ".DateFormatter::format($project->due_date).'.',
                    );
                }
            });

        Project::query()
            ->overdue()
            ->whereDoesntHave('notificationLogs', fn ($query) => $query->where('event', NotificationEvent::ProjectOverdue))
            ->with('user')
            ->chunkById(200, function (Collection $projects) use ($notifier): void {
                foreach ($projects as $project) {
                    $notifier->once(
                        $project,
                        NotificationEvent::ProjectOverdue,
                        $project->user,
                        'Project Overdue',
                        "The project \"{$project->name}\" was due on ".DateFormatter::format($project->due_date).' and is not complete.',
                    );
                }
            });
    }
}
