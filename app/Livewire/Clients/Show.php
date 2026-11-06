<?php

declare(strict_types=1);

namespace App\Livewire\Clients;

use App\Actions\DeleteClient;
use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    private const int RELATED_LIMIT = 10;

    #[Locked]
    public Client $client;

    public ClientForm $form;

    public function mount(Client $client): void
    {
        Gate::authorize('view', $client);

        $this->client = $client;
        $this->form->setClient($client);
    }

    public function save(): void
    {
        Gate::authorize('update', $this->client);

        $this->client->update($this->form->validate());

        $this->dispatch('close-dialog', name: 'client-form');
        $this->dispatch('toast', type: 'success', message: 'Client updated.');
    }

    #[On('dialog-closed')]
    public function dialogClosed(string $name): void
    {
        if ($name === 'client-form') {
            $this->form->setClient($this->client);
            $this->resetValidation();
        }
    }

    public function delete(DeleteClient $deleteClient): void
    {
        Gate::authorize('delete', $this->client);

        $deleteClient->handle($this->client);

        session()->flash('toast', ['type' => 'success', 'message' => 'Client deleted.']);

        $this->redirectRoute('clients.index', navigate: true);
    }

    public function render(): View
    {
        Gate::authorize('view', $this->client);

        return view('livewire.clients.show', [
            'incompleteProjectCount' => $this->client->projects()->incomplete()->count(),
            'completedProjectCount' => $this->client->projects()->completed()->count(),
            'unresolvedIssueCount' => $this->client->issues()->unresolved()->count(),
            'resolvedIssueCount' => $this->client->issues()->resolved()->count(),
            'contactCount' => $this->client->contacts()->count(),
            'projects' => $this->client->projects()->incomplete()
                ->orderByRaw('due_date is null')
                ->orderBy('due_date')
                ->limit(self::RELATED_LIMIT)
                ->get(),
            'issues' => $this->client->issues()->unresolved()
                ->orderByDesc('priority')
                ->orderBy('created_at')
                ->limit(self::RELATED_LIMIT)
                ->get(),
            'relatedLimit' => self::RELATED_LIMIT,
        ])->title($this->client->name);
    }
}
