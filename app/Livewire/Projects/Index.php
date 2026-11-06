<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Livewire\Concerns\SelectsOwnedRecords;
use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Forms\ProjectForm;
use App\Models\Client;
use App\Models\Project;
use App\Support\TableFilter;
use App\Support\TableFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Projects')]
class Index extends Component
{
    use SelectsOwnedRecords;
    use WithDataTable;

    public ProjectForm $form;

    public function save(): void
    {
        $this->currentUser()->projects()->create($this->form->values());

        $this->form->reset();
        $this->dispatch('close-dialog', name: 'project-form');
        $this->dispatch('toast', type: 'success', message: 'Project added.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'project-form') {
            $this->form->reset();
            $this->resetValidation();
        }
    }

    public function render(): View
    {
        return view('livewire.projects.index', [
            ...$this->tableData(),
            'clientOptions' => $this->clientOptions(),
        ]);
    }

    /**
     * @return Builder<Project>
     */
    protected function query(): Builder
    {
        return Project::query()
            ->ownedBy($this->currentUser())
            ->with('client')
            ->addSelect([
                'client_name' => Client::query()->select('name')->whereColumn('clients.id', 'projects.client_id'),
            ]);
    }

    protected function searchColumns(): array
    {
        return ['projects.name'];
    }

    protected function sortColumns(): array
    {
        return ['name', 'client_name', 'start_date', 'due_date', 'completed_date', 'updated_at'];
    }

    protected function defaultSort(): string
    {
        return 'updated_at';
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
        return [
            'started' => TableFilters::started(),
            'due' => TableFilters::pastDue(),
            'completed' => TableFilters::completed('completed_date', 'Status', 'Completed', 'Incomplete'),
            'client' => TableFilters::client($this->currentUser()),
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Name' => fn (Project $project) => $project->name,
            'Client' => fn (Project $project) => $project->client->name,
            'Description' => fn (Project $project) => $project->description,
            'Start date' => fn (Project $project) => $project->start_date?->toDateString(),
            'Due date' => fn (Project $project) => $project->due_date?->toDateString(),
            'Completed' => fn (Project $project) => $project->completed_date?->toDateString(),
            'Updated' => fn (Project $project) => $project->updated_at?->toDateTimeString(),
        ];
    }

    protected function exportName(): string
    {
        return 'projects';
    }
}
