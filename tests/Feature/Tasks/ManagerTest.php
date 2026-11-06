<?php

declare(strict_types=1);

use App\Livewire\Issues\Show as IssueShow;
use App\Livewire\Projects\Show as ProjectShow;
use App\Livewire\Tasks\Manager;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-06-15 12:00:00');
    $this->user = User::factory()->create();
    $this->project = Project::factory()->for($this->user)->create();
    $this->issue = Issue::factory()->for($this->user)->create();
    $this->actingAs($this->user);
});

dataset('parents', [
    'project' => fn () => 'project',
    'issue' => fn () => 'issue',
]);

function taskParent(string $parent, Project $project, Issue $issue): Project|Issue
{
    return $parent === 'project' ? $project : $issue;
}

function taskFor(Project|Issue $parent, array $attributes = []): Task
{
    $factory = $parent instanceof Project ? Task::factory()->forProject($parent) : Task::factory()->forIssue($parent);

    return $factory->create($attributes);
}

it('lists only the parent\'s tasks', function (string $parent) {
    $model = taskParent($parent, $this->project, $this->issue);
    taskFor($model, ['title' => 'Mine']);
    Task::factory()->create(['title' => 'Elsewhere']);
    taskFor($model instanceof Project ? $this->issue : $this->project, ['title' => 'Sibling']);

    Livewire::test(Manager::class, ['taskable' => $model])
        ->assertSee('Mine')
        ->assertDontSee('Elsewhere')
        ->assertDontSee('Sibling');
})->with('parents');

it('adds a task and tells the page', function (string $parent) {
    $model = taskParent($parent, $this->project, $this->issue);

    Livewire::test(Manager::class, ['taskable' => $model])
        ->call('create')
        ->assertDispatched('open-dialog', name: 'task-form')
        ->set('form.title', 'Do it')
        ->set('form.description', 'Details')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-dialog', name: 'task-form')
        ->assertDispatched('tasks-changed')
        ->assertSet('form.title', '');

    expect($model->tasks()->sole())
        ->title->toBe('Do it')
        ->taskable_type->toBe($parent);
})->with('parents');

it('validates tasks', function (array $input, string $error) {
    Livewire::test(Manager::class, ['taskable' => $this->project])
        ->set(['form.title' => 'T', 'form.description' => 'D', ...$input])
        ->call('save')
        ->assertHasErrors([$error])
        ->assertNotDispatched('close-dialog');

    expect(Task::query()->count())->toBe(0);
})->with([
    'missing title' => [['form.title' => ''], 'form.title'],
    'long title' => [['form.title' => str_repeat('a', 256)], 'form.title'],
    'missing description' => [['form.description' => ''], 'form.description'],
    'bad date' => [['form.due_date' => 'nope'], 'form.due_date'],
    'due before start' => [['form.start_date' => '2026-06-10', 'form.due_date' => '2026-06-01'], 'form.due_date'],
]);

it('stores dates on project tasks', function () {
    Livewire::test(Manager::class, ['taskable' => $this->project])
        ->set(['form.title' => 'T', 'form.description' => 'D', 'form.start_date' => '2026-06-01', 'form.due_date' => '2026-06-20'])
        ->call('save')
        ->assertHasNoErrors();

    expect(Task::query()->sole()->due_date->toDateString())->toBe('2026-06-20');
});

it('hides dates on issue tasks and discards any that are sent', function () {
    $component = Livewire::test(Manager::class, ['taskable' => $this->issue])
        ->assertDontSeeHtml('id="task-due"')
        ->assertDontSeeHtml('sortBy(\'due_date\')')
        ->set(['form.title' => 'T', 'form.description' => 'D', 'form.start_date' => '2026-06-01', 'form.due_date' => '2026-06-20'])
        ->call('save')
        ->assertHasNoErrors();

    expect(Task::query()->sole())
        ->start_date->toBeNull()
        ->due_date->toBeNull();

    $component->call('sortBy', 'due_date');

    expect($component->viewData('activeSort'))->toBe('updated_at');
});

it('shows dates on project tasks', function () {
    Livewire::test(Manager::class, ['taskable' => $this->project])
        ->assertSeeHtml('id="task-due"')
        ->assertSeeHtml('sortBy(\'due_date\')');
});

it('sorts project tasks by due date descending by default', function () {
    taskFor($this->project, ['title' => 'Early', 'due_date' => '2026-06-20']);
    taskFor($this->project, ['title' => 'Late', 'due_date' => '2026-08-20']);

    $component = Livewire::test(Manager::class, ['taskable' => $this->project]);

    expect($component->viewData('rows')->pluck('title')->all())->toBe(['Late', 'Early'])
        ->and($component->viewData('activeDirection'))->toBe('desc');
});

it('sorts issue tasks by last update by default', function () {
    taskFor($this->issue, ['title' => 'Old', 'updated_at' => now()->subDay()]);
    taskFor($this->issue, ['title' => 'New', 'updated_at' => now()]);

    expect(Livewire::test(Manager::class, ['taskable' => $this->issue])->viewData('rows')->pluck('title')->all())->toBe(['New', 'Old']);
});

it('searches and filters', function () {
    taskFor($this->project, ['title' => 'Overdue thing', 'due_date' => '2026-06-14']);
    taskFor($this->project, ['title' => 'Later', 'due_date' => '2026-06-30']);
    taskFor($this->project, ['title' => 'Done', 'completed_date' => '2026-06-01']);

    $titles = fn ($component) => $component->viewData('rows')->pluck('title')->sort()->values()->all();
    $manager = fn () => Livewire::test(Manager::class, ['taskable' => $this->project]);

    expect($titles($manager()->set('search', 'over')))->toBe(['Overdue thing'])
        ->and($titles($manager()->set('filters.due', 'yes')))->toBe(['Overdue thing'])
        ->and($titles($manager()->set('filters.completed', 'yes')))->toBe(['Done'])
        ->and($titles($manager()->set('filters.completed', 'no')))->toBe(['Later', 'Overdue thing']);
});

it('offers only the completed filter on issue tasks', function () {
    expect(array_keys(Livewire::test(Manager::class, ['taskable' => $this->issue])->viewData('tableFilters')))->toBe(['completed']);
});

it('edits and completes a task, keeping the first completion date', function () {
    $task = taskFor($this->project, ['title' => 'Old']);

    $component = Livewire::test(Manager::class, ['taskable' => $this->project])
        ->call('edit', $task->uuid)
        ->assertSet('form.title', 'Old')
        ->assertSeeHtml('id="task-completed"')
        ->set('form.title', 'New')
        ->set('form.completed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('tasks-changed')
        ->assertSet('editingUuid', null);

    expect($task->fresh())
        ->title->toBe('New')
        ->completed_date->not->toBeNull();

    $this->travel(2)->days();

    $component->call('edit', $task->uuid)->assertSet('form.completed', true)->call('save');

    expect($task->fresh()->completed_date->toDateString())->toBe('2026-06-15');

    $component->call('edit', $task->uuid)->set('form.completed', false)->call('save');

    expect($task->fresh()->completed_date)->toBeNull();
});

it('confirms before deleting', function (string $parent) {
    $task = taskFor(taskParent($parent, $this->project, $this->issue), ['title' => 'Doomed']);

    $component = Livewire::test(Manager::class, ['taskable' => $task->taskable])
        ->call('confirmDelete', $task->uuid)
        ->assertDispatched('open-dialog', name: 'task-delete')
        ->assertSee('This permanently deletes Doomed.');

    expect($task->fresh())->not->toBeNull();

    $component->call('delete')->assertDispatched('tasks-changed');

    expect($task->fresh())->toBeNull();
})->with('parents');

it('exports the parent\'s tasks with the right columns', function () {
    taskFor($this->project, ['title' => 'P task', 'due_date' => '2026-07-01']);
    taskFor($this->issue, ['title' => 'I task']);

    $content = fn ($component) => base64_decode(data_get($component->effects, 'download.content'));

    $project = Livewire::test(Manager::class, ['taskable' => $this->project])->call('export', 'csv')->assertFileDownloaded('project-tasks-2026-06-15.csv');
    $issue = Livewire::test(Manager::class, ['taskable' => $this->issue])->call('export', 'csv')->assertFileDownloaded('issue-tasks-2026-06-15.csv');

    expect($content($project))->toContain('P task')->toContain('Due date')->not->toContain('I task')
        ->and($content($issue))->toContain('I task')->not->toContain('Due date')->not->toContain('P task');
});

it('refreshes the parent page counts', function () {
    taskFor($this->project)->update(['completed_date' => '2026-06-01']);

    $page = Livewire::test(ProjectShow::class, ['project' => $this->project])->assertViewHas('completedTaskCount', 1);
    taskFor($this->project);
    $page->dispatch('tasks-changed')->assertViewHas('incompleteTaskCount', 1);

    $page = Livewire::test(IssueShow::class, ['issue' => $this->issue])->assertViewHas('incompleteTaskCount', 0);
    taskFor($this->issue);
    $page->dispatch('tasks-changed')->assertViewHas('incompleteTaskCount', 1);
});

it('locks record identifiers', function (string $property) {
    Livewire::test(Manager::class, ['taskable' => $this->project])->set($property, 1);
})->with(['taskable', 'editingUuid', 'deletingUuid'])->throws('Cannot update locked property');

describe('someone else\'s task', function () {
    it('cannot be reached through this parent', function (Closure $task, string $method) {
        $record = $task($this->issue);

        Livewire::test(Manager::class, ['taskable' => $this->project])->call($method, $record->uuid)->assertNotFound();

        expect($record->fresh())->not->toBeNull();
    })->with([
        'sibling parent' => fn (Issue $issue) => Task::factory()->forIssue($issue)->create(),
        'other user' => fn (Issue $issue) => Task::factory()->create(),
    ])->with(['edit', 'confirmDelete']);
});

describe('another user', function () {
    it('cannot mount the manager', function (string $parent) {
        $model = taskParent($parent, $this->project, $this->issue);

        $this->actingAs(User::factory()->create());

        Livewire::test(Manager::class, ['taskable' => $model])->assertNotFound();
    })->with('parents');

    it('cannot act on a component they obtained', function (Closure $action) {
        $task = taskFor($this->project, ['title' => 'Safe']);
        $component = Livewire::test(Manager::class, ['taskable' => $this->project]);

        $this->actingAs(User::factory()->create());

        $action($component, $task->uuid)->assertForbidden();

        expect($task->fresh()->title)->toBe('Safe');
    })->with([
        'create' => fn ($component, string $uuid) => $component->call('create'),
        'edit' => fn ($component, string $uuid) => $component->call('edit', $uuid),
        'save' => fn ($component, string $uuid) => $component->call('save'),
        'confirm delete' => fn ($component, string $uuid) => $component->call('confirmDelete', $uuid),
        'delete' => fn ($component, string $uuid) => $component->call('delete'),
        'export' => fn ($component, string $uuid) => $component->call('export', 'csv'),
    ]);
});
