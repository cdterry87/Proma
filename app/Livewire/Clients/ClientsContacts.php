<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;
use App\Models\ClientContact;

class ClientsContacts extends Component
{
    use WithModal;

    public $client_id, $client_name;
    public $name, $title, $email, $phone, $phone_ext;
    public $active = true;

    #[On('edit')]
    public function edit($id)
    {
        $client = Client::find($id);
        if ($client) {
            $this->client_id = $client->id;
            $this->client_name = $client->name;
        }
    }

    public function render()
    {
        $clientContacts = ClientContact::query()
            ->where('client_id', $this->client_id)
            ->get();

        return view('livewire.clients.clients-contacts', [
            'clientContacts' => $clientContacts,
        ]);
    }

    public function addContact()
    {
        $this->validate([
            'name' => 'required|max:255',
            'title' => 'required|max:255',
            'email' => 'required|nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'phone_ext' => 'nullable|max:5',
        ]);

        ClientContact::create([
            'client_id' => $this->client_id,
            'name' => $this->name,
            'title' => $this->title,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_ext' => $this->phone_ext,
            'active' => $this->active,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function deleteContact($id)
    {
        ClientContact::destroy($id);
    }
}
