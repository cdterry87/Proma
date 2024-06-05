<?php

namespace App\Livewire\Issues;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Issue;

class IssuesView extends Component
{
    public $issue;

    public function mount(Issue $issue)
    {
        $this->issue = $issue;
    }

    public function render()
    {
        return view('livewire.issues.issues-view');
    }

    #[On('refreshData')]
    public function getIssue()
    {
        $this->issue = Issue::find($this->issue->id);
    }
}
