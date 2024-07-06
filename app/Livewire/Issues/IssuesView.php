<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Livewire\Component;
use Livewire\Attributes\On;

class IssuesView extends Component
{
    public $issue;

    public $incompleteTasksCount = 0;
    public $completeTasksCount = 0;

    public function mount(Issue $issue)
    {
        $this->issue = $issue;

        if ($this->issue->user_id !== auth()->id()) abort(403);

        $this->getIssueStats();
    }

    public function render()
    {
        return view('livewire.issues.issues-view', [
            'priorityCodes' => Issue::getPriorityCodes(),
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

        $this->incompleteTasksCount = $this->issue->incomplete_tasks->count();
        $this->completeTasksCount = $this->issue->complete_tasks->count();
    }

    public function deleteIssue()
    {
        $this->issue->delete();

        return redirect()->route('issues');
    }
}
