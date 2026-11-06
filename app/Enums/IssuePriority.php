<?php

declare(strict_types=1);

namespace App\Enums;

enum IssuePriority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;

    public function label(): string
    {
        return $this->name;
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Low => 'success',
            self::Medium => 'info',
            self::High => 'warning',
            self::Critical => 'error',
        };
    }

    public function staleAfterDays(): int
    {
        return match ($this) {
            self::Low => 30,
            self::Medium => 14,
            self::High => 7,
            self::Critical => 3,
        };
    }

    public function staleEvent(): NotificationEvent
    {
        return match ($this) {
            self::Low => NotificationEvent::IssueStaleLow,
            self::Medium => NotificationEvent::IssueStaleMedium,
            self::High => NotificationEvent::IssueStaleHigh,
            self::Critical => NotificationEvent::IssueStaleCritical,
        };
    }

    public function staleTitle(): string
    {
        return match ($this) {
            self::Low => 'Low Priority Issue',
            self::Medium => 'Medium Issue',
            self::High => 'High Issue',
            self::Critical => 'Critical Issue',
        };
    }
}
