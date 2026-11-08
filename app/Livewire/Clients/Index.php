<?php

declare(strict_types=1);

namespace App\Livewire\Clients;

use App\Enums\DemoRecord;
use App\Livewire\Concerns\HasCurrentUser;
use App\Livewire\Concerns\LimitsDemoRecords;
use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use App\Support\TableFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clients')]
class Index extends Component
{
    use HasCurrentUser;
    use LimitsDemoRecords;
    use WithDataTable;

    public ClientForm $form;

    public function save(): void
    {
        if ($this->demoLimitReached(DemoRecord::Client)) {
            return;
        }

        $this->currentUser()->clients()->create($this->form->validate());

        $this->form->reset();
        $this->dispatch('close-dialog', name: 'client-form');
        $this->dispatch('toast', type: 'success', message: 'Client added.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'client-form') {
            $this->form->reset();
            $this->resetValidation();
        }
    }

    public function render(): View
    {
        return view('livewire.clients.index', $this->tableData());
    }

    /**
     * @return Builder<Client>
     */
    protected function query(): Builder
    {
        return Client::query()
            ->ownedBy($this->currentUser())
            ->withCount([
                'contacts',
                'projects as incomplete_projects_count' => fn (Builder $query) => $query->whereNull('completed_date'),
                'issues as unresolved_issues_count' => fn (Builder $query) => $query->whereNull('resolved_date'),
            ]);
    }

    protected function searchColumns(): array
    {
        return ['name'];
    }

    protected function sortColumns(): array
    {
        return ['name', 'incomplete_projects_count', 'unresolved_issues_count', 'contacts_count', 'active', 'updated_at'];
    }

    protected function defaultSort(): string
    {
        return 'name';
    }

    /**
     * @return array<string, TableFilter>
     */
    protected function tableFilters(): array
    {
        return [
            'active' => new TableFilter(
                'Status',
                ['1' => 'Active', '0' => 'Inactive'],
                fn (Builder $query, string $value) => $query->where('active', $value === '1'),
            ),
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Name' => fn (Client $client) => $client->name,
            'Description' => fn (Client $client) => $client->description,
            'Incomplete projects' => fn (Client $client) => $client->incomplete_projects_count,
            'Unresolved issues' => fn (Client $client) => $client->unresolved_issues_count,
            'Contacts' => fn (Client $client) => $client->contacts_count,
            'Active' => fn (Client $client) => $client->active ? 'Yes' : 'No',
            'Updated' => fn (Client $client) => $client->updated_at?->toDateTimeString(),
        ];
    }

    protected function exportName(): string
    {
        return 'clients';
    }
}
