<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Project;

class ProjectsView extends Component
{
    public $project;

    public $unresolvedIssuesCount = 0;
    public $resolvedIssuesCount = 0;
    public $incompleteTasksCount = 0;
    public $completeTasksCount = 0;

    public function mount(Project $project)
    {
        $this->project = $project;

        $this->getProjectStats();
    }

    public function render()
    {
        return view('livewire.projects.projects-view');
    }

    #[On('refreshData')]
    public function getProject()
    {
        $this->project = Project::find($this->project->id);
    }

    #[On('refreshData')]
    public function getProjectStats()
    {
        if (!$this->project) return;

        $this->unresolvedIssuesCount = $this->project->unresolved_issues->count();
        $this->resolvedIssuesCount = $this->project->resolved_issues->count();
        $this->incompleteTasksCount = $this->project->incomplete_tasks->count();
        $this->completeTasksCount = $this->project->complete_tasks->count();
    }
}
