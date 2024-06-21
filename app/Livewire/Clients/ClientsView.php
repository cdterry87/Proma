<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;

class ClientsView extends Component
{
    public $client;

    public $incompleteProjectsCount = 0;
    public $completeProjectsCount = 0;
    public $unresolvedIssuesCount = 0;
    public $resolvedIssuesCount = 0;

    public function mount(Client $client)
    {
        $this->client = $client;

        $this->getClientStats();
    }

    public function render()
    {
        return view('livewire.clients.clients-view');
    }

    #[On('refreshData')]
    public function getClient()
    {
        $this->client = Client::find($this->client->id);
    }

    #[On('refreshData')]
    public function getClientStats()
    {
        if (!$this->client) return;

        $this->incompleteProjectsCount = $this->client->incomplete_projects->count();
        $this->completeProjectsCount = $this->client->complete_projects->count();
        $this->unresolvedIssuesCount = $this->client->unresolved_issues->count();
        $this->resolvedIssuesCount = $this->client->resolved_issues->count();
    }
}
