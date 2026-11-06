<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTasks
{
    /**
     * @return MorphMany<Task, $this>
     */
    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    /**
     * @return MorphMany<Task, $this>
     */
    public function incompleteTasks(): MorphMany
    {
        return $this->tasks()->whereNull('completed_date');
    }

    /**
     * Database foreign keys cannot cascade to polymorphic children.
     */
    public function deleteTasks(): void
    {
        $this->tasks()->lazyById()->each(fn (Task $task) => $task->delete());
    }
}
