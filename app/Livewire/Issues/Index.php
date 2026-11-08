<?php

declare(strict_types=1);

namespace App\Livewire\Issues;

use App\Enums\DemoRecord;
use App\Enums\IssuePriority;
use App\Livewire\Concerns\LimitsDemoRecords;
use App\Livewire\Concerns\SelectsOwnedRecords;
use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Forms\IssueForm;
use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Support\TableFilter;
use App\Support\TableFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Issues')]
class Index extends Component
{
    use LimitsDemoRecords;
    use SelectsOwnedRecords;
    use WithDataTable;

    public IssueForm $form;

    public function updatedFormClientId(): void
    {
        $this->form->project_id = '';
    }

    public function save(): void
    {
        if ($this->demoLimitReached(DemoRecord::Issue)) {
            return;
        }

        $this->currentUser()->issues()->create($this->form->values());

        $this->form->reset();
        $this->dispatch('close-dialog', name: 'issue-form');
        $this->dispatch('toast', type: 'success', message: 'Issue added.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'issue-form') {
            $this->form->reset();
            $this->resetValidation();
        }
    }

    public function render(): View
    {
        return view('livewire.issues.index', [
            ...$this->tableData(),
            'clientOptions' => $this->clientOptions(),
            'projectOptions' => $this->projectOptions($this->form->client_id !== '' ? (int) $this->form->client_id : null),
        ]);
    }

    /**
     * @return Builder<Issue>
     */
    protected function query(): Builder
    {
        return Issue::query()
            ->ownedBy($this->currentUser())
            ->with(['client', 'project'])
            ->addSelect([
                'client_name' => Client::query()->select('name')->whereColumn('clients.id', 'issues.client_id'),
                'project_name' => Project::query()->select('name')->whereColumn('projects.id', 'issues.project_id'),
            ]);
    }

    protected function searchColumns(): array
    {
        return ['issues.name'];
    }

    protected function sortColumns(): array
    {
        return ['name', 'priority', 'client_name', 'project_name', 'resolved_date', 'updated_at'];
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
            'priority' => new TableFilter(
                'Priority',
                collect(IssuePriority::cases())->mapWithKeys(fn (IssuePriority $priority) => [$priority->value => $priority->label()])->all(),
                fn (Builder $query, string $value) => $query->where('priority', (int) $value),
            ),
            'resolved' => TableFilters::completed('resolved_date', 'Status', 'Resolved', 'Unresolved'),
            'client' => TableFilters::client($this->currentUser()),
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Name' => fn (Issue $issue) => $issue->name,
            'Priority' => fn (Issue $issue) => $issue->priority->label(),
            'Client' => fn (Issue $issue) => $issue->client->name,
            'Project' => fn (Issue $issue) => $issue->project?->name,
            'Description' => fn (Issue $issue) => $issue->description,
            'Resolved' => fn (Issue $issue) => $issue->resolved_date?->toDateString(),
            'Updated' => fn (Issue $issue) => $issue->updated_at?->toDateTimeString(),
        ];
    }

    protected function exportName(): string
    {
        return 'issues';
    }
}
