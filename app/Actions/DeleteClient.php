<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DeleteClient
{
    public function __construct(
        private readonly DeleteProject $deleteProject,
        private readonly DeleteIssue $deleteIssue,
    ) {}

    public function handle(Client $client): void
    {
        DB::transaction(function () use ($client): void {
            $client->projects()->lazyById()->each(fn (Project $project) => $this->deleteProject->handle($project));
            $client->issues()->lazyById()->each(fn (Issue $issue) => $this->deleteIssue->handle($issue));
            $client->delete();
        });
    }
}
