<?php

namespace App\Livewire\Projects;

use App\Models\Team;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class ProjectsForm extends Component
{
    use WithModal;

    public $model_id;
    public $client_id, $team_id;
    public $name, $description, $start_date, $due_date, $completed_date;
    public $assigned_to, $assigned_by, $created_by, $updated_by;

    public function render()
    {
        $clients = Client::all();
        $teams = Team::all();
        $users = User::all();

        return view('livewire.projects.projects-form', [
            'clients' => $clients,
            'teams' => $teams,
            'users' => $users,
        ]);
    }

    #[On('edit')]
    public function edit($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->model_id = $id;
            $this->name = $project->name;
            $this->description = $project->description;
            $this->start_date = $project->start_date;
            $this->due_date = $project->due_date;
            $this->completed_date = $project->completed_date;
            $this->assigned_to = $project->assigned_to;
            $this->assigned_by = $project->assigned_by;
            $this->created_by = $project->created_by;
            $this->updated_by = $project->updated_by;
        }
    }

    public function save()
    {
        $this->validate([
            'client_id' => 'nullable|exists:clients,id',
            'team_id' => 'nullable|exists:teams,id',
            'name' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'completed_date' => $this->completed_date,
            'assigned_to' => $this->assigned_to,
            'assigned_by' => $this->assigned_by,
            'updated_by' => auth()->id(),
        ];

        if ($this->model_id) {
            Project::where('id', $this->model_id)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            Project::create($data);
        }

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Project saved successfully.');
    }
}
