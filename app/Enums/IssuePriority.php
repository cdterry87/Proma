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

    public function staleAfterDays(): int
    {
        return match ($this) {
            self::Low => 30,
            self::Medium => 14,
            self::High => 7,
            self::Critical => 3,
        };
    }
}
