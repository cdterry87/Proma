<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Forms\TaskForm;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Support\TableFilter;
use App\Support\TableFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The tasks of a project or an issue. Only project tasks have dates.
 */
class Manager extends Component
{
    use WithDataTable;

    #[Locked]
    public Project|Issue $taskable;

    #[Locked]
    public ?string $editingUuid = null;

    #[Locked]
    public ?string $deletingUuid = null;

    #[Locked]
    public string $deletingTitle = '';

    public TaskForm $form;

    public function mount(Project|Issue $taskable): void
    {
        Gate::authorize('view', $taskable);

        $this->taskable = $taskable;
    }

    /**
     * Tasks authorize through their parent, on every request.
     */
    public function hydrate(): void
    {
        Gate::authorize('update', $this->taskable);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->dispatch('open-dialog', name: 'task-form');
    }

    public function edit(string $uuid): void
    {
        $task = $this->task($uuid);

        $this->resetForm();
        $this->editingUuid = $task->uuid;
        $this->form->setTask($task);
        $this->dispatch('open-dialog', name: 'task-form');
    }

    public function save(): void
    {
        if ($this->editingUuid === null) {
            $this->taskable->tasks()->create($this->form->values($this->withDates()));
        } else {
            $task = $this->task($this->editingUuid);
            $task->update($this->form->values($this->withDates(), $task));
        }

        $message = $this->editingUuid === null ? 'Task added.' : 'Task updated.';

        $this->resetForm();
        $this->dispatch('close-dialog', name: 'task-form');
        $this->dispatch('tasks-changed');
        $this->dispatch('toast', type: 'success', message: $message);
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'task-form') {
            $this->resetForm();
        }
    }

    public function confirmDelete(string $uuid): void
    {
        $task = $this->task($uuid);

        $this->deletingUuid = $task->uuid;
        $this->deletingTitle = $task->title;
        $this->dispatch('open-dialog', name: 'task-delete');
    }

    public function delete(): void
    {
        if ($this->deletingUuid === null) {
            return;
        }

        $this->task($this->deletingUuid)->delete();
        $this->reset('deletingUuid', 'deletingTitle');
        $this->dispatch('tasks-changed');
        $this->dispatch('toast', type: 'success', message: 'Task deleted.');
    }

    public function render(): View
    {
        return view('livewire.tasks.manager', [
            ...$this->tableData(),
            'withDates' => $this->withDates(),
        ]);
    }

    /**
     * @return Builder<Task>
     */
    protected function query(): Builder
    {
        return Task::query()->whereMorphedTo('taskable', $this->taskable);
    }

    protected function searchColumns(): array
    {
        return ['title'];
    }

    protected function sortColumns(): array
    {
        return $this->withDates()
            ? ['title', 'start_date', 'due_date', 'completed_date']
            : ['title', 'completed_date', 'updated_at'];
    }

    protected function defaultSort(): string
    {
        return $this->withDates() ? 'due_date' : 'updated_at';
    }

    protected function defaultDirection(): string
    {
        return 'desc';
    }

    /**
     * @return array<string, TableFilter>
     */
    protected function tableFilters(): array
    {
        $completed = TableFilters::completed('completed_date', 'Status', 'Completed', 'Incomplete');

        return $this->withDates()
            ? ['started' => TableFilters::started(), 'due' => TableFilters::pastDue(), 'completed' => $completed]
            : ['completed' => $completed];
    }

    protected function exportColumns(): array
    {
        return [
            'Title' => fn (Task $task) => $task->title,
            'Description' => fn (Task $task) => $task->description,
            ...($this->withDates() ? [
                'Start date' => fn (Task $task) => $task->start_date?->toDateString(),
                'Due date' => fn (Task $task) => $task->due_date?->toDateString(),
            ] : []),
            'Completed' => fn (Task $task) => $task->completed_date?->toDateString(),
        ];
    }

    protected function exportName(): string
    {
        return $this->taskable->getMorphClass().'-tasks';
    }

    private function withDates(): bool
    {
        return $this->taskable instanceof Project;
    }

    private function task(string $uuid): Task
    {
        return $this->taskable->tasks()->where('uuid', $uuid)->firstOrFail();
    }

    private function resetForm(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->editingUuid = null;
    }
}
