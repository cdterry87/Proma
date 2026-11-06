<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Status derived from the due_date and completed_date columns.
 *
 * SQLite stores date casts with a time and MySQL does not, so the scopes
 * compare with half-open ranges, which give the same result on both and can
 * still use an index.
 */
trait TracksCompletion
{
    public function isCompleted(): bool
    {
        return $this->completed_date !== null;
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted()
            && $this->due_date !== null
            && today()->gt($this->due_date);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereNull('completed_date');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_date');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNull('completed_date')
            ->where('due_date', '<', today()->toDateString());
    }

    /**
     * Incomplete and due from today through the given number of days ahead.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeDueWithin(Builder $query, int $days): Builder
    {
        return $query->whereNull('completed_date')
            ->where('due_date', '>=', today()->toDateString())
            ->where('due_date', '<', today()->addDays($days + 1)->toDateString());
    }
}
