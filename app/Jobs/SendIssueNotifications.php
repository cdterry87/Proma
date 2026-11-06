<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\IssuePriority;
use App\Models\Issue;
use App\Support\Notifier;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class SendIssueNotifications
{
    use Dispatchable;

    public function handle(Notifier $notifier): void
    {
        foreach (IssuePriority::cases() as $priority) {
            $days = $priority->staleAfterDays();
            $event = $priority->staleEvent();

            Issue::query()
                ->unresolved()
                ->where('priority', $priority)
                ->where('created_at', '<', now()->subDays($days))
                ->whereDoesntHave('notificationLogs', fn ($query) => $query->where('event', $event))
                ->with('user')
                ->chunkById(200, function (Collection $issues) use ($notifier, $priority, $days, $event): void {
                    foreach ($issues as $issue) {
                        $notifier->once(
                            $issue,
                            $event,
                            $issue->user,
                            $priority->staleTitle(),
                            "The {$priority->label()} priority issue \"{$issue->name}\" has been open for more than {$days} days.",
                        );
                    }
                });
        }
    }
}
