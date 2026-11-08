<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\DemoRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait LimitsDemoRecords
{
    /**
     * Whether a demo account is at its cap for this kind of record. If it is,
     * the visitor is told why nothing was added.
     */
    private function demoLimitReached(DemoRecord $record): bool
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $record->reachedBy($user)) {
            return false;
        }

        $this->dispatch('toast', type: 'error', message: $record->message());

        return true;
    }
}
