<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Project;

class ProjectsView extends Component
{
    public $project;

    public $incompleteIssuesCount = 0;
    public $completeIssuesCount = 0;
    public $incompleteTasksCount = 0;
    public $completeTasksCount = 0;
    public $uploadsTotalCount = 0;
    public $uploadsTotalSize = 0;

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

        $this->incompleteIssuesCount = $this->project->getIncompleteTasks()->count();
        $this->completeIssuesCount = $this->project->getCompleteTasks()->count();
        $this->incompleteTasksCount = $this->project->getIncompleteTasks()->count();
        $this->completeTasksCount = $this->project->getCompleteTasks()->count();
        $this->uploadsTotalCount = $this->project->uploads()->count();
        $this->uploadsTotalSize = $this->project->uploads()->sum('size');
    }
}
