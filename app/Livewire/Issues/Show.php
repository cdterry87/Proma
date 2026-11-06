<?php

declare(strict_types=1);

namespace App\Livewire\Issues;

use App\Actions\DeleteIssue;
use App\Livewire\Concerns\SelectsOwnedRecords;
use App\Livewire\Forms\IssueForm;
use App\Models\Issue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use SelectsOwnedRecords;

    #[Locked]
    public Issue $issue;

    public IssueForm $form;

    public function mount(Issue $issue): void
    {
        Gate::authorize('view', $issue);

        $this->issue = $issue;
        $this->form->setIssue($issue);
    }

    public function updatedFormClientId(): void
    {
        $this->form->project_id = '';
    }

    public function save(): void
    {
        Gate::authorize('update', $this->issue);

        $this->issue->update($this->form->values($this->issue));

        $this->dispatch('close-dialog', name: 'issue-form');
        $this->dispatch('toast', type: 'success', message: 'Issue updated.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'issue-form') {
            $this->form->setIssue($this->issue);
            $this->resetValidation();
        }
    }

    #[On('tasks-changed')]
    public function tasksChanged(): void
    {
        // Receiving the event is enough to render the new counts.
    }

    public function delete(DeleteIssue $deleteIssue): void
    {
        Gate::authorize('delete', $this->issue);

        $deleteIssue->handle($this->issue);

        session()->flash('toast', ['type' => 'success', 'message' => 'Issue deleted.']);

        $this->redirectRoute('issues.index', navigate: true);
    }

    public function render(): View
    {
        Gate::authorize('view', $this->issue);

        $selectedClient = $this->form->client_id !== '' ? (int) $this->form->client_id : null;

        return view('livewire.issues.show', [
            'client' => $this->issue->client()->sole(),
            'project' => $this->issue->project()->first(),
            'incompleteTaskCount' => $this->issue->incompleteTasks()->count(),
            'completedTaskCount' => $this->issue->tasks()->whereNotNull('completed_date')->count(),
            'clientOptions' => $this->clientOptions($this->issue->client_id),
            'projectOptions' => $this->projectOptions(
                $selectedClient,
                $selectedClient === $this->issue->client_id ? $this->issue->project_id : null,
            ),
        ])->title($this->issue->name);
    }
}
