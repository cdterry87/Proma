<?php

namespace App\Livewire\Projects;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\ProjectTask;
use Livewire\Attributes\On;

class ProjectsTasks extends Component
{
    use WithModal;

    public $project_id, $project_name, $project_team_id;
    public $task_id, $title, $description, $start_date, $due_date, $completed_date, $assigned_to;

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
            ->orderBy('name')
            ->get();

        return view('livewire.projects.projects-tasks', [
            'assignableUsers' => $assignableUsers
        ]);
    }

    public function saveTask()
    {
        $this->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($this->task_id) {
            $projectTask = ProjectTask::find($this->task_id);
        } else {
            $projectTask = new ProjectTask();
            $projectTask->project_id = $this->project_id;
            $projectTask->created_by = auth()->id();
        }

        $projectTask->title = $this->title;
        $projectTask->description = $this->description;
        $projectTask->assigned_to = $this->assigned_to;
        $projectTask->start_date = $this->start_date;
        $projectTask->due_date = $this->due_date;
        $projectTask->completed_date = $this->completed_date;
        $projectTask->updated_by = auth()->id();
        $projectTask->save();

        $this->dispatch('refreshData');

        session()->flash('success', 'Task saved successfully.');
    }

    #[On('editTask')]
    public function editTask($id)
    {
        $projectTask = ProjectTask::query()
            ->with('project')
            ->find($id);
        if ($projectTask) {
            $this->task_id = $projectTask->id;
            $this->title = $projectTask->title;
            $this->description = $projectTask->description;
            $this->assigned_to = $projectTask->assigned_to;
            $this->start_date = $projectTask->start_date;
            $this->due_date = $projectTask->due_date;
            $this->completed_date = $projectTask->completed_date;

            if ($projectTask->project) {
                $this->project_id = $projectTask->project->id;
                $this->project_name = $projectTask->project->name;
                $this->project_team_id = $projectTask->project->team_id;
            }
        }

        $this->dispatch('refreshData');
    }

    #[On('deleteTask')]
    public function deleteTask($taskId, $projectId)
    {
        $projectTask = ProjectTask::query()
            ->where('id', $taskId)
            ->where('project_id', $projectId)
            ->first();

        if ($projectTask) {
            $projectTask->delete();
        }

        $this->dispatch('refreshData');
    }
}
