<?php

declare(strict_types=1);

use App\Actions\DeleteClient;
use App\Actions\DeleteIssue;
use App\Actions\DeleteProject;
use App\Enums\NotificationEvent;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\NotificationLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\UserNotification;

/**
 * Creates a client with a contact, a project with a task and an issue, and an
 * issue of its own with a task.
 *
 * @return array{client: Client, project: Project, projectIssue: Issue, issue: Issue}
 */
function clientTree(): array
{
    $client = Client::factory()->create();
    ClientContact::factory()->for($client)->create();

    $project = Project::factory()->forClient($client)->create();
    Task::factory()->forProject($project)->create();

    $projectIssue = Issue::factory()->forProject($project)->create();
    Task::factory()->forIssue($projectIssue)->create();

    $issue = Issue::factory()->forClient($client)->create();
    Task::factory()->forIssue($issue)->create();

    return compact('client', 'project', 'projectIssue', 'issue');
}

it('deletes an issue and its tasks', function () {
    ['issue' => $issue] = clientTree();

    app(DeleteIssue::class)->handle($issue);

    expect(Issue::query()->find($issue->id))->toBeNull()
        ->and(Task::query()->where('taskable_type', 'issue')->where('taskable_id', $issue->id)->exists())->toBeFalse()
        ->and(Task::query()->count())->toBe(2);
});

it('deletes a project with its issues and all of their tasks', function () {
    ['project' => $project, 'issue' => $issue] = clientTree();

    app(DeleteProject::class)->handle($project);

    expect(Project::query()->count())->toBe(0)
        ->and(Issue::query()->pluck('id')->all())->toBe([$issue->id])
        ->and(Task::query()->pluck('taskable_id')->all())->toBe([$issue->id]);
});

it('deletes a client and everything beneath it', function () {
    ['client' => $client] = clientTree();
    $unrelated = Task::factory()->create();

    app(DeleteClient::class)->handle($client);

    expect(Client::query()->whereKey($client->id)->exists())->toBeFalse()
        ->and(ClientContact::query()->count())->toBe(0)
        ->and(Issue::query()->count())->toBe(0)
        ->and(Project::query()->pluck('id')->all())->toBe([$unrelated->taskable_id])
        ->and(Task::query()->pluck('id')->all())->toBe([$unrelated->id]);
});

it('rolls back if a delete fails part way', function () {
    ['client' => $client] = clientTree();

    Project::deleting(fn () => throw new RuntimeException('Failed'));

    try {
        app(DeleteClient::class)->handle($client);
    } catch (RuntimeException) {
        // Expected.
    }

    expect(Client::query()->count())->toBe(1)
        ->and(Task::query()->count())->toBe(3);
});

it('clears the log and unlinks notifications when a subject is deleted', function (Closure $subject) {
    $record = $subject();
    $notification = UserNotification::factory()->create([
        'subject_type' => $record->getMorphClass(),
        'subject_id' => $record->id,
    ]);
    $record->notificationLogs()->create(['user_id' => $notification->user_id, 'event' => NotificationEvent::TaskDue]);

    $record instanceof Task ? $record->delete() : app($record instanceof Issue ? DeleteIssue::class : DeleteProject::class)->handle($record);

    $notification->refresh();

    expect(NotificationLog::query()->count())->toBe(0)
        ->and($notification->subject_type)->toBeNull()
        ->and($notification->subject_id)->toBeNull();
})->with([
    'project' => fn () => Project::factory()->create(),
    'issue' => fn () => Issue::factory()->create(),
    'task' => fn () => Task::factory()->create(),
]);

it('cascades contacts and owned records at the database level', function () {
    ['client' => $client] = clientTree();

    $client->user->delete();

    expect(Client::query()->count())->toBe(0)
        ->and(ClientContact::query()->count())->toBe(0)
        ->and(Project::query()->count())->toBe(0)
        ->and(Issue::query()->count())->toBe(0);
});
