<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationEvent: string
{
    case IssueStaleLow = 'issue_stale_low';
    case IssueStaleMedium = 'issue_stale_medium';
    case IssueStaleHigh = 'issue_stale_high';
    case IssueStaleCritical = 'issue_stale_critical';
    case ProjectDue = 'project_due';
    case ProjectOverdue = 'project_overdue';
    case TaskDue = 'task_due';
    case TaskOverdue = 'task_overdue';
}
