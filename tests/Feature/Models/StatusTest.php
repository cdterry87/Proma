<?php

declare(strict_types=1);

use App\Enums\IssuePriority;
use App\Enums\NotificationEvent;
use App\Models\Client;
use App\Models\Issue;
use App\Models\NotificationLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\DateFormatter;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\UniqueConstraintViolationException;

beforeEach(function () {
    $this->travelTo('2026-06-15 13:00:00');
});

function projectDue(?string $date, ?string $completed = null): Project
{
    return Project::factory()->create(['due_date' => $date, 'completed_date' => $completed]);
}

it('reads a stored date back as an immutable date', function () {
    expect(projectDue('2026-06-20')->fresh()->due_date)
        ->toBeInstanceOf(DateTimeImmutable::class)
        ->format('Y-m-d')->toBe('2026-06-20');
});

it('finds overdue records up to but not including today', function () {
    $yesterday = projectDue('2026-06-14');
    projectDue('2026-06-15');
    projectDue('2026-06-16');
    projectDue(null);
    projectDue('2026-06-14', '2026-06-14');

    expect(Project::query()->overdue()->pluck('id')->all())->toBe([$yesterday->id]);
});

it('finds records due from today through the end of the window', function () {
    projectDue('2026-06-14');
    $today = projectDue('2026-06-15');
    $lastDay = projectDue('2026-06-25');
    projectDue('2026-06-26');
    projectDue(null);
    projectDue('2026-06-20', '2026-06-15');

    expect(Project::query()->dueWithin(10)->pluck('id')->all())->toBe([$today->id, $lastDay->id]);
});

it('derives status from dates', function (?string $due, ?string $completed, bool $isCompleted, bool $isOverdue) {
    $project = projectDue($due, $completed)->fresh();

    expect($project->isCompleted())->toBe($isCompleted)
        ->and($project->isOverdue())->toBe($isOverdue);
})->with([
    'past due' => ['2026-06-14', null, false, true],
    'due today' => ['2026-06-15', null, false, false],
    'no due date' => [null, null, false, false],
    'completed late' => ['2026-06-14', '2026-06-15', true, false],
]);

it('applies the same status rules to tasks', function () {
    $overdue = Task::factory()->create(['due_date' => '2026-06-14']);
    $done = Task::factory()->completed()->create();

    expect(Task::query()->overdue()->pluck('id')->all())->toBe([$overdue->id])
        ->and(Task::query()->incomplete()->pluck('id')->all())->toBe([$overdue->id])
        ->and(Task::query()->completed()->pluck('id')->all())->toBe([$done->id])
        ->and($overdue->fresh()->isOverdue())->toBeTrue();
});

it('filters issues by resolution', function () {
    $open = Issue::factory()->create();
    $closed = Issue::factory()->resolved()->create();

    expect(Issue::query()->unresolved()->pluck('id')->all())->toBe([$open->id])
        ->and(Issue::query()->resolved()->pluck('id')->all())->toBe([$closed->id])
        ->and($open->isResolved())->toBeFalse()
        ->and($closed->fresh()->isResolved())->toBeTrue();
});

it('filters active clients and defaults new ones to active', function () {
    $active = User::factory()->create()->clients()->create(['name' => 'A', 'description' => 'B']);
    Client::factory()->inactive()->create();

    expect(Client::query()->active()->pluck('id')->all())->toBe([$active->id])
        ->and($active->active)->toBeTrue();
});

it('loads incomplete tasks and unresolved issues without the finished ones', function () {
    $project = Project::factory()->create();
    $open = Task::factory()->forProject($project)->create();
    Task::factory()->forProject($project)->completed()->create();
    $issue = Issue::factory()->forProject($project)->create();
    Issue::factory()->forProject($project)->resolved()->create();

    expect($project->incompleteTasks()->pluck('id')->all())->toBe([$open->id])
        ->and($project->unresolvedIssues()->pluck('id')->all())->toBe([$issue->id])
        ->and($open->taskable->is($project))->toBeTrue();
});

it('casts the priority to its enum', function () {
    $issue = Issue::factory()->create(['priority' => IssuePriority::High]);

    expect($issue->fresh()->priority)->toBe(IssuePriority::High);
});

it('labels priorities and sets their stale windows', function (IssuePriority $priority, int $value, string $label, int $days) {
    expect($priority->value)->toBe($value)
        ->and($priority->label())->toBe($label)
        ->and($priority->staleAfterDays())->toBe($days);
})->with([
    [IssuePriority::Low, 1, 'Low', 30],
    [IssuePriority::Medium, 2, 'Medium', 14],
    [IssuePriority::High, 3, 'High', 7],
    [IssuePriority::Critical, 4, 'Critical', 3],
]);

it('renders a missing date as the fallback rather than today', function () {
    $date = new DateTimeImmutable('2026-03-09');

    expect(DateFormatter::format($date))->toBe('Mar 9, 2026')
        ->and(DateFormatter::format(null))->toBe('Not set')
        ->and(DateFormatter::format(null, 'N/A'))->toBe('N/A');
});

it('stores short morph aliases', function () {
    $task = Task::factory()->forIssue(Issue::factory()->create())->create();

    expect($task->fresh()->taskable_type)->toBe('issue')
        ->and(Relation::morphMap())->toHaveKeys(['client', 'project', 'issue', 'task']);
});

it('tracks read notifications', function () {
    $unread = UserNotification::factory()->create();
    $read = UserNotification::factory()->read()->create();

    expect(UserNotification::query()->unread()->pluck('id')->all())->toBe([$unread->id])
        ->and($unread->isRead())->toBeFalse()
        ->and($read->fresh()->isRead())->toBeTrue()
        ->and($unread->subject)->toBeNull();
});

it('links a notification to its subject', function () {
    $issue = Issue::factory()->create();
    $notification = UserNotification::factory()->create(['subject_type' => 'issue', 'subject_id' => $issue->id]);

    expect($notification->subject->is($issue))->toBeTrue();
});

it('logs each event once per subject', function () {
    $project = Project::factory()->create();
    $attributes = ['user_id' => $project->user_id, 'event' => NotificationEvent::ProjectDue];

    $log = $project->notificationLogs()->create($attributes);

    expect($log->fresh()->event)->toBe(NotificationEvent::ProjectDue)
        ->and($log->subject->is($project))->toBeTrue()
        ->and($log->user->is($project->user))->toBeTrue()
        ->and($log->created_at)->not->toBeNull();

    $project->notificationLogs()->create(['user_id' => $project->user_id, 'event' => NotificationEvent::ProjectOverdue]);

    expect(NotificationLog::query()->count())->toBe(2);

    $project->notificationLogs()->create($attributes);
})->throws(UniqueConstraintViolationException::class);
