<?php

declare(strict_types=1);

namespace App\Livewire\Contacts;

use App\Livewire\Concerns\WithDataTable;
use App\Livewire\Forms\ContactForm;
use App\Models\Client;
use App\Models\ClientContact;
use App\Support\TableFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Manager extends Component
{
    use WithDataTable;

    #[Locked]
    public Client $client;

    #[Locked]
    public ?string $editingUuid = null;

    #[Locked]
    public ?string $deletingUuid = null;

    #[Locked]
    public string $deletingName = '';

    public ContactForm $form;

    public function mount(Client $client): void
    {
        Gate::authorize('view', $client);

        $this->client = $client;
    }

    /**
     * Contacts authorize through their client, on every request.
     */
    public function hydrate(): void
    {
        Gate::authorize('update', $this->client);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->dispatch('open-dialog', name: 'contact-form');
    }

    public function edit(string $uuid): void
    {
        $contact = $this->contact($uuid);

        $this->resetForm();
        $this->editingUuid = $contact->uuid;
        $this->form->setContact($contact);
        $this->dispatch('open-dialog', name: 'contact-form');
    }

    public function save(): void
    {
        $values = $this->form->values();

        if ($this->editingUuid === null) {
            $this->client->contacts()->create($values);
        } else {
            $this->contact($this->editingUuid)->update($values);
        }

        $message = $this->editingUuid === null ? 'Contact added.' : 'Contact updated.';

        $this->resetForm();
        $this->dispatch('close-dialog', name: 'contact-form');
        $this->dispatch('contacts-changed');
        $this->dispatch('toast', type: 'success', message: $message);
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'contact-form') {
            $this->resetForm();
        }
    }

    public function confirmDelete(string $uuid): void
    {
        $contact = $this->contact($uuid);

        $this->deletingUuid = $contact->uuid;
        $this->deletingName = $contact->name;
        $this->dispatch('open-dialog', name: 'contact-delete');
    }

    public function delete(): void
    {
        if ($this->deletingUuid === null) {
            return;
        }

        $this->contact($this->deletingUuid)->delete();
        $this->reset('deletingUuid', 'deletingName');
        $this->dispatch('contacts-changed');
        $this->dispatch('toast', type: 'success', message: 'Contact deleted.');
    }

    public function render(): View
    {
        return view('livewire.contacts.manager', $this->tableData());
    }

    /**
     * @return Builder<ClientContact>
     */
    protected function query(): Builder
    {
        return ClientContact::query()->whereBelongsTo($this->client);
    }

    protected function searchColumns(): array
    {
        return ['name', 'title', 'email'];
    }

    protected function sortColumns(): array
    {
        return ['name', 'title', 'email', 'phone', 'active'];
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
            'Name' => fn (ClientContact $contact) => $contact->name,
            'Title' => fn (ClientContact $contact) => $contact->title,
            'Email' => fn (ClientContact $contact) => $contact->email,
            'Phone' => fn (ClientContact $contact) => $contact->phone,
            'Ext.' => fn (ClientContact $contact) => $contact->phone_ext,
            'Active' => fn (ClientContact $contact) => $contact->active ? 'Yes' : 'No',
        ];
    }

    protected function exportName(): string
    {
        return 'contacts';
    }

    private function contact(string $uuid): ClientContact
    {
        return $this->client->contacts()->where('uuid', $uuid)->firstOrFail();
    }

    private function resetForm(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->editingUuid = null;
    }
}
