<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\ProjectTask;
use Livewire\Attributes\On;

class ProjectsTasksForm extends Component
{
    use WithModal;

    public $project_id, $project_name;
    public $task_id, $title, $description, $start_date, $due_date, $completed_date;

    #[On('getProject')]
    public function getProject($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->project_id = $project->id;
            $this->project_name = $project->name;
        }
    }

    public function render()
    {
        return view('livewire.projects.projects-tasks-form');
    }

    public function saveTask()
    {
        $this->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
        ]);

        ProjectTask::updateOrCreate([
            'id' => $this->task_id,
            'project_id' => $this->project_id,
        ], [
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'completed_date' => $this->completed_date,
        ]);

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
            $this->start_date = $projectTask->start_date;
            $this->due_date = $projectTask->due_date;
            $this->completed_date = $projectTask->completed_date;

            if ($projectTask->project) {
                $this->project_id = $projectTask->project->id;
                $this->project_name = $projectTask->project->name;
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
