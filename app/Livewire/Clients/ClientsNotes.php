<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ClientNote;
use App\Models\User;
use App\Traits\WithModal;
use Livewire\Component;
use Livewire\Attributes\On;

class ClientsNotes extends Component
{
    use WithModal;

    public $client_id, $client_name;
    public $note_id, $note;

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
        return view('livewire.clients.clients-notes');
    }

    public function saveNote()
    {
        $this->validate([
            'note' => 'required',
        ]);

        if ($this->note_id) {
            $clientNote = ClientNote::find($this->note_id);
        } else {
            $clientNote = new ClientNote();
            $clientNote->client_id = $this->client_id;
            $clientNote->created_by = auth()->id();
        }

        $clientNote->note = $this->note;
        $clientNote->updated_by = auth()->id();
        $clientNote->save();

        $this->dispatch('refreshData');
    }

    #[On('editNote')]
    public function editNote($id)
    {
        $clientNote = ClientNote::query()
            ->with('client')
            ->find($id);
        if ($clientNote) {
            $this->note_id = $clientNote->id;
            $this->note = $clientNote->note;

            $this->client_name = $clientNote->client->name;
        }

        $this->dispatch('refreshData');
    }

    #[On('deleteNote')]
    public function deleteNote($noteId, $clientId)
    {
        $clientNote = ClientNote::query()
            ->where('id', $noteId)
            ->where('client_id', $clientId)
            ->first();

        if ($clientNote) {
            $clientNote->delete();
        }

        $this->dispatch('refreshData');
    }
}
