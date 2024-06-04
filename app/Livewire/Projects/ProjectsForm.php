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

    public function render()
    {
        $clients = Client::all();
        $users = User::all();

        return view('livewire.projects.projects-form', [
            'clients' => $clients,
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
            $this->client_id = $project->client_id;
            $this->team_id = $project->team_id;
            $this->start_date = $project->start_date;
            $this->due_date = $project->due_date;
            $this->completed_date = $project->completed_date;
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
        ]);

        Project::updateOrCreate([
            'id' => $this->model_id,
        ], [
            'name' => $this->name,
            'description' => $this->description,
            'client_id' => $this->client_id ? $this->client_id : null,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'completed_date' => $this->completed_date,
        ]);

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Project saved successfully.');
    }
}
