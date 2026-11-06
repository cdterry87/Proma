<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\IsNotificationSubject;
use App\Models\Concerns\TracksCompletion;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A task on a project or an issue. Tasks authorize through their parent.
 */
#[Fillable(['title', 'description', 'start_date', 'due_date', 'completed_date'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

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
     * @return MorphTo<Model, $this>
     */
    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }
}
