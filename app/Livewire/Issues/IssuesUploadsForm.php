<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueUpload;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class IssuesUploadsForm extends Component
{
    use WithModal;
    use WithFileUploads;

    public $issue_id, $issue_name;
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

    #[On('getIssue')]
    public function getIssue($id)
    {
        $issue = Issue::find($id);
        if ($issue) {
            $this->issue_id = $issue->id;
            $this->issue_name = $issue->name;
        }
    }

    public function render()
    {
        return view('livewire.issues.issues-uploads-form');
    }

    public function uploadFiles()
    {
        $this->validate([
            'files.*' => 'required|max:10240|mimes:doc,docx,pdf,ppt,pptx,rtf,txt,csv,xls,xlsx,gif,jpg,jpeg,png,svg,zip,rar,7z'
        ]);

        $uploads = [];
        foreach ($this->files as $file) {
            $filename = $file->getClientOriginalName();

            $uploads[] = [
                'issue_id' => $this->issue_id,
                'path' => $file->storeAs('issues/' . $this->issue_id, $filename),
                'name' => $filename,
                'type' => $file->extension(),
                'size' => $file->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $file->storeAs('issues/' . $this->issue_id, $filename);
        }

        IssueUpload::insert($uploads);

        $this->files = [];
        $this->uploadIteration++;

        $this->dispatch('refreshData');
    }

    #[On('deleteFile')]
    public function deleteFile($fileId, $issueId)
    {
        $issueUpload = IssueUpload::query()
            ->where('id', $fileId)
            ->where('issueId', $issueId)
            ->first();

        if ($issueUpload) {
            $issueUpload->delete();
        }

        $this->dispatch('refreshData');
    }

    #[On('downloadFile')]
    public function downloadFile($fileId, $issueId)
    {
        $file = IssueUpload::query()
            ->where('issue_id', $issueId)
            ->where('id', $fileId)
            ->first();

        if (!$file) return;

        return Storage::download($file->path, $file->name);
    }
}
