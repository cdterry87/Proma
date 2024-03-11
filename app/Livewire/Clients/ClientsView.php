<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;

class ClientsView extends Component
{
    public $client;

    public function mount(Client $client)
    {
        $this->client = $client;
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
}
