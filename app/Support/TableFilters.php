<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Filters shared by tables. Date columns compare with half-open ranges, as in
 * TracksCompletion, so that SQLite and MySQL agree.
 */
final class TableFilters
{
    public static function started(): TableFilter
    {
        return new TableFilter(
            'Started',
            ['yes' => 'Started', 'no' => 'Not started'],
            function (Builder $query, string $value): void {
                $tomorrow = today()->addDay()->toDateString();

                $value === 'yes'
                    ? $query->where('start_date', '<', $tomorrow)
                    : $query->whereNull('start_date')->orWhere('start_date', '>=', $tomorrow);
            },
        );
    }

    public static function pastDue(): TableFilter
    {
        return new TableFilter(
            'Due',
            ['yes' => 'Past due', 'no' => 'Not due'],
            function (Builder $query, string $value): void {
                $today = today()->toDateString();

                $value === 'yes'
                    ? $query->whereNull('completed_date')->where('due_date', '<', $today)
                    : $query->whereNotNull('completed_date')->orWhereNull('due_date')->orWhere('due_date', '>=', $today);
            },
        );
    }

    public static function completed(string $column, string $label, string $done, string $notDone): TableFilter
    {
        return new TableFilter(
            $label,
            ['yes' => $done, 'no' => $notDone],
            fn (Builder $query, string $value) => $value === 'yes'
                ? $query->whereNotNull($column)
                : $query->whereNull($column),
        );
    }
}
