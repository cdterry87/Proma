<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->other = User::factory()->create();
});

it('allows the owner every action', function (Closure $create) {
    $record = $create($this->owner);

    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser($this->owner)->allows($ability, $record))->toBeTrue();
    }
})->with('owned records');

it('allows a demo user every action on their own records', function (Closure $create) {
    $demo = User::factory()->demo()->create();
    $record = $create($demo);

    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser($demo)->allows($ability, $record))->toBeTrue();
    }
})->with('owned records');

it('denies another user every action', function (Closure $create) {
    $record = $create($this->owner);

    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser($this->other)->denies($ability, $record))->toBeTrue();
    }
})->with('owned records');

it('responds 404 when viewing another user\'s record', function (Closure $create) {
    $response = Gate::forUser($this->other)->inspect('view', $create($this->owner));

    expect($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
})->with('owned records');

it('responds 403 to actions on another user\'s record', function (Closure $create, string $ability) {
    $record = $create($this->owner);

    try {
        Gate::forUser($this->other)->authorize($ability, $record);
        $this->fail('The action was allowed.');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBeNull();
    }
})->with('owned records')->with(['update', 'delete']);

it('denies guests every action', function (Closure $create) {
    $record = $create($this->owner);

    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser(null)->denies($ability, $record))->toBeTrue();
    }
})->with('owned records');

it('authorizes a contact through its client', function () {
    $contact = ClientContact::factory()
        ->for(Client::factory()->for($this->owner))
        ->create();

    expect(Gate::forUser($this->owner)->allows('update', $contact->client))->toBeTrue()
        ->and(Gate::forUser($this->other)->denies('update', $contact->client))->toBeTrue();
});

it('authorizes a task through its parent', function (Closure $parent) {
    $task = $parent($this->owner);

    expect(Gate::forUser($this->owner)->allows('update', $task->taskable))->toBeTrue()
        ->and(Gate::forUser($this->other)->denies('update', $task->taskable))->toBeTrue();
})->with([
    'project' => fn (User $owner) => Task::factory()->forProject(Project::factory()->for($owner)->create())->create(),
    'issue' => fn (User $owner) => Task::factory()->forIssue(Issue::factory()->for($owner)->create())->create(),
]);

it('scopes queries to the owner', function (Closure $create) {
    $mine = $create($this->owner);
    $create($this->other);

    expect($mine::query()->ownedBy($this->owner)->pluck('id')->all())->toBe([$mine->id]);
})->with('owned records');

it('never accepts the owner through mass assignment', function (Closure $create) {
    $record = $create($this->owner);

    $record->update(['user_id' => $this->other->id]);
})->with('owned records')->throws(MassAssignmentException::class);

it('links factory records to the same owner', function () {
    $project = Project::factory()->create();
    $issue = Issue::factory()->forProject($project)->create();

    expect($project->client->user_id)->toBe($project->user_id)
        ->and($issue->user_id)->toBe($project->user_id)
        ->and($issue->client_id)->toBe($project->client_id)
        ->and(Issue::factory()->create()->client->user_id)->not->toBeNull();
});
