<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The records a demo account can create, each with a cap. Anyone can sign in
 * to the demo, so without caps a script could fill the database before the
 * nightly reset.
 */
enum DemoRecord: string
{
    case Client = 'clients';
    case Project = 'projects';
    case Issue = 'issues';
    case Contact = 'contacts';
    case Task = 'tasks';

    public function limit(): int
    {
        return (int) config("proma.demo.limits.{$this->value}");
    }

    public function count(User $user): int
    {
        return match ($this) {
            self::Client => $user->clients()->count(),
            self::Project => $user->projects()->count(),
            self::Issue => $user->issues()->count(),
            self::Contact => ClientContact::query()
                ->whereHas('client', fn (Builder $query) => $query->whereBelongsTo($user))
                ->count(),
            self::Task => Task::query()
                ->whereHasMorph('taskable', [Project::class, Issue::class], fn (Builder $query) => $query->whereBelongsTo($user))
                ->count(),
        };
    }

    public function reachedBy(User $user): bool
    {
        return $user->is_demo && $this->count($user) >= $this->limit();
    }

    public function message(): string
    {
        return "The demo allows up to {$this->limit()} {$this->value}. Delete one to add another.";
    }
}
