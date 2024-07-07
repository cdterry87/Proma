<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueNotificationSent;
use Illuminate\Bus\Queueable;
use App\Models\UserNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IssuesNotificationsJob implements ShouldQueue
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
        $this->handleResolvedIssueNotifications();
        $this->handleCriticalPriorityIssueNotifications();
        $this->handleHighPriorityIssueNotifications();
        $this->handleMediumPriorityIssueNotifications();
        $this->handleLowPriorityIssueNotifications();
    }

    protected function handleResolvedIssueNotifications()
    {
        $issues = Issue::query()
            ->where('resolved_date', '<=', now()->toDateString())
            ->whereDoesntHave('resolvedNotificationSent')
            ->get();

        foreach ($issues as $issue) {
            UserNotification::create([
                'user_id' => $issue->user_id,
                'subject' => 'Issue Resolved',
                'message' => 'Your issue has been resolved: ' . $issue->name,
            ]);

            IssueNotificationSent::create([
                'user_id' => $issue->user_id,
                'issue_id' => $issue->id,
                'priority' => $issue->priority,
                'resolved' => $issue->resolved_date ? true : false,
            ]);
        }
    }

    protected function handleCriticalPriorityIssueNotifications()
    {
        $issues = Issue::query()
            ->where('priority', 4)
            ->whereNull('resolved_date')
            ->where('created_at', '<', now()->subDays(3))
            ->whereDoesntHave('criticalPriorityNotificationSent')
            ->get();

        foreach ($issues as $issue) {
            UserNotification::create([
                'user_id' => $issue->user_id,
                'subject' => 'Critical Issue',
                'message' => 'You have an unresolved critical priority issue that has been open for 3 days: ' . $issue->name,
            ]);

            IssueNotificationSent::create([
                'user_id' => $issue->user_id,
                'issue_id' => $issue->id,
                'priority' => $issue->priority,
                'resolved' => $issue->resolved_date ? true : false,
            ]);
        }
    }

    protected function handleHighPriorityIssueNotifications()
    {
        $issues = Issue::query()
            ->where('priority', 3)
            ->whereNull('resolved_date')
            ->where('created_at', '<', now()->subDays(7))
            ->whereDoesntHave('highPriorityNotificationSent')
            ->get();

        foreach ($issues as $issue) {
            UserNotification::create([
                'user_id' => $issue->user_id,
                'subject' => 'High Issue',
                'message' => 'You have an unresolved high priority issue that has been open for 7 days: ' . $issue->name,
            ]);

            IssueNotificationSent::create([
                'user_id' => $issue->user_id,
                'issue_id' => $issue->id,
                'priority' => $issue->priority,
                'resolved' => $issue->resolved_date ? true : false,
            ]);
        }
    }

    protected function handleMediumPriorityIssueNotifications()
    {
        $issues = Issue::query()
            ->where('priority', 2)
            ->whereNull('resolved_date')
            ->where('created_at', '<', now()->subDays(14))
            ->whereDoesntHave('mediumPriorityNotificationSent')
            ->get();

        foreach ($issues as $issue) {
            UserNotification::create([
                'user_id' => $issue->user_id,
                'subject' => 'Medium Issue',
                'message' => 'You have an unresolved medium priority issue that has been open for 14 days: ' . $issue->name,
            ]);

            IssueNotificationSent::create([
                'user_id' => $issue->user_id,
                'issue_id' => $issue->id,
                'priority' => $issue->priority,
                'resolved' => $issue->resolved_date ? true : false,
            ]);
        }
    }

    protected function handleLowPriorityIssueNotifications()
    {
        $issues = Issue::query()
            ->where('priority', 1)
            ->whereNull('resolved_date')
            ->where('created_at', '<', now()->subDays(30))
            ->whereDoesntHave('lowPriorityNotificationSent')
            ->get();

        foreach ($issues as $issue) {
            UserNotification::create([
                'user_id' => $issue->user_id,
                'subject' => 'Low Priority Issue',
                'message' => 'You have an unresolved low priority issue that has been open for 30 days: ' . $issue->name,
            ]);

            IssueNotificationSent::create([
                'user_id' => $issue->user_id,
                'issue_id' => $issue->id,
                'priority' => $issue->priority,
                'resolved' => $issue->resolved_date ? true : false,
            ]);
        }
    }
}
