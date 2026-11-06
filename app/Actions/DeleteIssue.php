<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Issue;
use Illuminate\Support\Facades\DB;

class DeleteIssue
{
    public function handle(Issue $issue): void
    {
        DB::transaction(function () use ($issue): void {
            $issue->deleteTasks();
            $issue->delete();
        });
    }
}
