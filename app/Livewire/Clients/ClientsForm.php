<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class ClientsForm extends Component
{
    use WithModal;

    public $model_id, $user_id;
    public $active = true;
    public $name, $description;

    public function render()
    {
        return view('livewire.clients.clients-form');
    }

    #[On('edit')]
    public function edit($id)
    {
        $client = Client::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        if ($client) {
            $this->model_id = $id;
            $this->user_id = $client->user_id;
            $this->active = !!$client->active;
            $this->name = $client->name;
            $this->description = $client->description;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

        Client::updateOrCreate([
            'id' => $this->model_id,
        ], [
            'user_id' => auth()->id(),
            'active' => $this->active,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Client saved successfully.');
    }
}
