<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Options for client and project selects. A record's current value is always
 * included, so editing never drops a link to an inactive client or a completed
 * project.
 */
trait SelectsOwnedRecords
{
    use HasCurrentUser;

    /**
     * @return Collection<int, string>
     */
    private function clientOptions(?int $current = null): Collection
    {
        return Client::query()
            ->ownedBy($this->currentUser())
            ->where(fn (Builder $query) => $query->where('active', true)->orWhere('id', $current))
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * @return Collection<int, string>
     */
    private function projectOptions(?int $clientId, ?int $current = null): Collection
    {
        if ($clientId === null) {
            return collect();
        }

        return Project::query()
            ->ownedBy($this->currentUser())
            ->where('client_id', $clientId)
            ->where(fn (Builder $query) => $query->whereNull('completed_date')->orWhere('id', $current))
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
