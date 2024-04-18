<?php

namespace App\Livewire\Projects;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class ProjectsAssignmentsForm extends Component
{
    use WithModal;

    public $project_id, $project_name, $project_team_id;
    public $assignment_id, $assigned_to, $assigned_by;

    #[On('getProject')]
    public function getProject($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->project_id = $project->id;
            $this->project_name = $project->name;
            $this->project_team_id = $project->team_id;
        }
    }

    public function render()
    {
        $assignableUsers = User::query()
            ->when($this->project_team_id, function ($query) {
                $query->whereHas('teams', function ($query) {
                    $query->where('team_id', $this->project_team_id);
                });
            })
            ->whereDoesntHave('projects_assignments', function ($query) {
                $query->where('project_id', $this->project_id);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.projects.projects-assignments-form', [
            'assignableUsers' => $assignableUsers
        ]);
    }

    public function saveAssignment()
    {
        $this->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($this->assignment_id) {
            $projectAssignment = ProjectAssignment::find($this->assignment_id);
        } else {
            $projectAssignment = new ProjectAssignment();
            $projectAssignment->project_id = $this->project_id;
        }

        $projectAssignment->assigned_to = $this->assigned_to;
        $projectAssignment->assigned_by = $this->assigned_by;
        $projectAssignment->save();

        $this->dispatch('refreshData');

        session()->flash('success', 'Assignment saved successfully.');
    }

    #[On('deleteAssignment')]
    public function deleteAssignment($assignmentId, $projectId)
    {
        if (!$projectId) return;

        $projectAssignment = ProjectAssignment::query()
            ->where('id', $assignmentId)
            ->where('project_id', $projectId)
            ->first();

        if ($projectAssignment) {
            $projectAssignment->delete();
        }

        $this->dispatch('refreshData');
    }
}
