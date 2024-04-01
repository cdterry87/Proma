<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\TeamUpload;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TeamsUploads extends Component
{
    use WithModal;
    use WithFileUploads;

    public $team_id, $team_name;
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

    #[On('getTeam')]
    public function getTeam($id)
    {
        $team = Team::find($id);
        if ($team) {
            $this->team_id = $team->id;
            $this->team_name = $team->name;
        }
    }

    public function render()
    {
        return view('livewire.teams.teams-uploads');
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
                'team_id' => $this->team_id,
                'path' => $file->storeAs('teams/' . $this->team_id, $filename),
                'name' => $filename,
                'type' => $file->extension(),
                'size' => $file->getSize(),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $file->storeAs('teams/' . $this->team_id, $filename);
        }

        TeamUpload::insert($uploads);

        $this->files = [];
        $this->uploadIteration++;

        $this->dispatch('refreshData');
    }

    #[On('deleteFile')]
    public function deleteFile($fileId, $teamId)
    {
        $teamUpload = TeamUpload::query()
            ->where('id', $fileId)
            ->where('team_id', $teamId)
            ->first();

        if ($teamUpload) {
            $teamUpload->delete();
        }

        $this->dispatch('refreshData');
    }

    #[On('downloadFile')]
    public function downloadFile($fileId, $teamId)
    {
        $file = TeamUpload::query()
            ->where('team_id', $teamId)
            ->where('id', $fileId)
            ->first();

        if (!$file) return;

        return Storage::download($file->path, $file->name);
    }
}
