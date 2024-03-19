<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Project;

class ProjectsView extends Component
{
    public $project;

    public function mount(Project $project)
    {
        $this->project = $project;
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
}
