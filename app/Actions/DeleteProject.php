<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DeleteProject
{
    public function __construct(private readonly DeleteIssue $deleteIssue) {}

    public function handle(Project $project): void
    {
        DB::transaction(function () use ($project): void {
            $project->issues()->lazyById()->each(fn (Issue $issue) => $this->deleteIssue->handle($issue));
            $project->deleteTasks();
            $project->delete();
        });
    }
}
