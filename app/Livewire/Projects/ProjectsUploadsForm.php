<?php

namespace App\Livewire\Projects;

use App\Models\Team;
use App\Models\Project;
use App\Models\ProjectUpload;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\TeamUpload;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProjectsUploadsForm extends Component
{
    use WithModal;
    use WithFileUploads;

    public $project_id, $project_name;
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

    #[On('getProject')]
    public function getProject($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->project_id = $project->id;
            $this->project_name = $project->name;
        }
    }

    public function render()
    {
        return view('livewire.projects.projects-uploads-form');
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
                'project_id' => $this->project_id,
                'path' => $file->storeAs('projects/' . $this->project_id, $filename),
                'name' => $filename,
                'type' => $file->extension(),
                'size' => $file->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $file->storeAs('projects/' . $this->project_id, $filename);
        }

        ProjectUpload::insert($uploads);

        $this->files = [];
        $this->uploadIteration++;

        $this->dispatch('refreshData');
    }

    #[On('deleteFile')]
    public function deleteFile($fileId, $projectId)
    {
        $projectUpload = ProjectUpload::query()
            ->where('id', $fileId)
            ->where('project_id', $projectId)
            ->first();

        if ($projectUpload) {
            $projectUpload->delete();
        }

        $this->dispatch('refreshData');
    }

    #[On('downloadFile')]
    public function downloadFile($fileId, $projectId)
    {
        $file = ProjectUpload::query()
            ->where('project_id', $projectId)
            ->where('id', $fileId)
            ->first();

        if (!$file) return;

        return Storage::download($file->path, $file->name);
    }
}
