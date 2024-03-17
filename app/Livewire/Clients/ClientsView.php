<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ClientUpload;
use Illuminate\Support\Facades\Storage;

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

    #[On('download')]
    public function download($id)
    {
        $file = ClientUpload::query()
            ->where('client_id', $this->client->id)
            ->where('id', $id)
            ->first();

        if ($file) {
            return Storage::download($file->path, $file->name);
        }
    }
}
