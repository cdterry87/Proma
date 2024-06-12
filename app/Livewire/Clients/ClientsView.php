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
    public $incompleteIssuesCount = 0;
    public $completeIssuesCount = 0;

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

        $this->incompleteProjectsCount = $this->client->projects()->whereNull('completed_date')->count();
        $this->completeProjectsCount = $this->client->projects()->whereNotNull('completed_date')->count();
        $this->incompleteIssuesCount = $this->client->issues()->whereNull('completed_date')->count();
        $this->completeIssuesCount = $this->client->issues()->whereNotNull('completed_date')->count();
    }
}
