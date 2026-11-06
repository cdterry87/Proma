<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use App\Models\Concerns\HasPublicUuid;
use App\Models\Concerns\HasTasks;
use App\Models\Concerns\IsNotificationSubject;
use App\Models\Concerns\TracksCompletion;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property CarbonImmutable|null $start_date
 * @property CarbonImmutable|null $due_date
 * @property CarbonImmutable|null $completed_date
 */
#[Fillable(['client_id', 'name', 'description', 'start_date', 'due_date', 'completed_date'])]
class Project extends Model
{
    use BelongsToUser;

    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use HasPublicUuid;
    use HasTasks;
    use IsNotificationSubject;
    use TracksCompletion;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'immutable_date',
            'due_date' => 'immutable_date',
            'completed_date' => 'immutable_date',
        ];
    }

    public function url(): string
    {
        return route('projects.show', $this);
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function unresolvedIssues(): HasMany
    {
        return $this->issues()->whereNull('resolved_date');
    }
}
