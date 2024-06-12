<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\WithReferences;

class IssuesView extends Component
{
    use WithReferences;

    public $issue;

    public $incompleteTasksCount = 0;
    public $completeTasksCount = 0;
    public $uploadsTotalCount = 0;
    public $uploadsTotalSize = 0;

    public function mount(Issue $issue)
    {
        $this->issue = $issue;

        $this->getIssueStats();
    }

    public function render()
    {
        return view('livewire.issues.issues-view', [
            'issuePriorities' => $this->getIssuePriorities(),
        ]);
    }

    #[On('refreshData')]
    public function getIssue()
    {
        $this->issue = Issue::find($this->issue->id);
    }

    #[On('refreshData')]
    public function getIssueStats()
    {
        if (!$this->issue) return;

        $this->incompleteTasksCount = $this->issue->getIncompleteTasks()->count();
        $this->completeTasksCount = $this->issue->getCompleteTasks()->count();
        $this->uploadsTotalCount = $this->issue->uploads()->count();
        $this->uploadsTotalSize = $this->issue->uploads()->sum('size');
    }
}
