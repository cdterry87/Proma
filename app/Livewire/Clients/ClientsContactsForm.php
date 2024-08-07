<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;
use App\Models\ClientContact;

class ClientsContactsForm extends Component
{
    use WithModal;

    public $client_id, $client_name;
    public $contact_id, $name, $title, $email, $phone, $phone_ext;
    public $active = true;

    #[On('getClient')]
    public function getClient($id)
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

        return view('livewire.clients.clients-contacts-form', [
            'clientContacts' => $clientContacts,
        ]);
    }

    public function saveContact()
    {
        $this->validate([
            'name' => 'required|max:255',
            'title' => 'required|max:255',
            'email' => 'required|nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'phone_ext' => 'nullable|max:5',
        ]);

        $clientContact = ClientContact::updateOrCreate([
            'id' => $this->contact_id,
            'client_id' => $this->client_id,
        ], [
            'name' => $this->name,
            'title' => $this->title,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_ext' => $this->phone_ext,
            'active' => $this->active,
        ]);

        $this->contact_id = $clientContact->id;

        $this->dispatch('refreshData');
    }

    #[On('editContact')]
    public function editContact($id)
    {
        $clientContact = ClientContact::query()
            ->with('client')
            ->find($id);
        if ($clientContact) {
            $this->contact_id = $clientContact->id;
            $this->client_id = $clientContact->client_id;
            $this->name = $clientContact->name;
            $this->title = $clientContact->title;
            $this->email = $clientContact->email;
            $this->phone = $clientContact->phone;
            $this->phone_ext = $clientContact->phone_ext;
            $this->active = !!$clientContact->active;

            $this->client_name = $clientContact->client->name;
        }

        $this->dispatch('refreshData');
    }

    #[On('deleteContact')]
    public function deleteContact($contactId, $clientId)
    {
        $clientContact = ClientContact::query()
            ->where('id', $contactId)
            ->where('client_id', $clientId)
            ->first();

        if ($clientContact) {
            $clientContact->delete();
        }

        $this->dispatch('refreshData');
    }
}
