<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use App\Models\Concerns\HasTasks;
use App\Models\Concerns\IsNotificationSubject;
use App\Models\Concerns\TracksCompletion;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_id', 'name', 'description', 'start_date', 'due_date', 'completed_date'])]
class Project extends Model
{
    use BelongsToUser;

    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

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
