<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;
use App\Models\ClientUpload;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ClientsUploadsForm extends Component
{
    use WithModal;
    use WithFileUploads;

    public $client_id, $client_name;
    public $uploadIteration = 0;
    public $files = [];

    public function messages()
    {
        return [
            'files.required' => 'You must upload a valid file.',
            'files.max' => 'This file is too large. File uploads must be under 10MB.',
            'files.mimes' => 'This file type is not supported.'
        ];
    }

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
        return view('livewire.clients.clients-uploads-form');
    }

    public function uploadFile()
    {
        $this->validate([
            'files.*' => 'required|max:10240|mimes:doc,docx,pdf,ppt,pptx,rtf,txt,csv,xls,xlsx,gif,jpg,jpeg,png,svg,zip,rar,7z'
        ]);

        $uploads = [];
        foreach ($this->files as $file) {
            $filename = $file->getClientOriginalName();

            $uploads[] = [
                'client_id' => $this->client_id,
                'path' => $file->storeAs('clients/' . $this->client_id, $filename),
                'name' => $filename,
                'type' => $file->extension(),
                'size' => $file->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $file->storeAs('clients/' . $this->client_id, $filename);
        }

        ClientUpload::insert($uploads);

        $this->files = [];
        $this->uploadIteration++;

        $this->dispatch('refreshData');
    }

    #[On('deleteFile')]
    public function deleteUpload($fileId, $clientId)
    {
        $clientUpload = ClientUpload::query()
            ->where('id', $fileId)
            ->where('client_id', $clientId)
            ->first();

        if ($clientUpload) {
            $clientUpload->delete();
        }

        $this->dispatch('refreshData');
    }

    #[On('downloadFile')]
    public function downloadFile($fileId, $clientId)
    {
        $file = ClientUpload::query()
            ->where('client_id', $clientId)
            ->where('id', $fileId)
            ->first();

        if (!$file) return;

        return Storage::download($file->path, $file->name);
    }
}
