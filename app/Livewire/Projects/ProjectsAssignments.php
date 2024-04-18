<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ProjectAssignment;

class ProjectsAssignments extends Component
{
    public $projectId;

    public function getProject($id)
    {
        $this->projectId = $id;
    }

    #[On('refreshData')]
    public function render()
    {
        $assignments = ProjectAssignment::query()
            ->where('project_id', $this->projectId)
            ->get();

        return view('livewire.projects.projects-assignments', [
            'assignments' => $assignments
        ]);
    }
}
