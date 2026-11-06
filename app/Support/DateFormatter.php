<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeInterface;

final class DateFormatter
{
    private const string FORMAT = 'M j, Y';

    public static function format(?DateTimeInterface $date, string $fallback = 'Not set'): string
    {
        return $date?->format(self::FORMAT) ?? $fallback;
    }
}
