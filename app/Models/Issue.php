<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IssuePriority;
use App\Models\Concerns\BelongsToUser;
use App\Models\Concerns\HasTasks;
use App\Models\Concerns\IsNotificationSubject;
use Carbon\CarbonImmutable;
use Database\Factories\IssueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property IssuePriority $priority
 * @property CarbonImmutable|null $resolved_date
 */
#[Fillable(['client_id', 'project_id', 'name', 'description', 'priority', 'resolved_date'])]
class Issue extends Model
{
    use BelongsToUser;

    /** @use HasFactory<IssueFactory> */
    use HasFactory;

    use HasTasks;
    use IsNotificationSubject;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => IssuePriority::class,
            'resolved_date' => 'immutable_date',
        ];
    }

    public function url(): string
    {
        return route('issues.show', $this);
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isResolved(): bool
    {
        return $this->resolved_date !== null;
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->whereNull('resolved_date');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->whereNotNull('resolved_date');
    }
}
