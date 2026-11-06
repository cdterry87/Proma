<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\DeleteProject;
use App\Livewire\Concerns\SelectsOwnedRecords;
use App\Livewire\Forms\ProjectForm;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use SelectsOwnedRecords;

    #[Locked]
    public Project $project;

    public ProjectForm $form;

    public function mount(Project $project): void
    {
        Gate::authorize('view', $project);

        $this->project = $project;
        $this->form->setProject($project);
    }

    public function save(): void
    {
        Gate::authorize('update', $this->project);

        $values = $this->form->values($this->project);

        DB::transaction(function () use ($values): void {
            $this->project->update($values);

            // An issue must always belong to the same client as its project.
            $this->project->issues()->update(['client_id' => $values['client_id']]);
        });

        $this->dispatch('close-dialog', name: 'project-form');
        $this->dispatch('toast', type: 'success', message: 'Project updated.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'project-form') {
            $this->form->setProject($this->project);
            $this->resetValidation();
        }
    }

    #[On('tasks-changed')]
    public function tasksChanged(): void
    {
        // Receiving the event is enough to render the new counts.
    }

    public function delete(DeleteProject $deleteProject): void
    {
        Gate::authorize('delete', $this->project);

        $deleteProject->handle($this->project);

        session()->flash('toast', ['type' => 'success', 'message' => 'Project deleted.']);

        $this->redirectRoute('projects.index', navigate: true);
    }

    public function render(): View
    {
        Gate::authorize('view', $this->project);

        return view('livewire.projects.show', [
            'client' => $this->project->client()->sole(),
            'issues' => $this->project->unresolvedIssues()->orderByDesc('priority')->orderBy('created_at')->get(),
            'resolvedIssueCount' => $this->project->issues()->whereNotNull('resolved_date')->count(),
            'incompleteTaskCount' => $this->project->incompleteTasks()->count(),
            'completedTaskCount' => $this->project->tasks()->whereNotNull('completed_date')->count(),
            'clientOptions' => $this->clientOptions($this->project->client_id),
        ])->title($this->project->name);
    }
}
