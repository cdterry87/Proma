<?php

namespace App\Livewire\Issues;

use App\Models\Team;
use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class Form extends Component
{
    use WithModal;

    public $model_id;
    public $description, $priority, $resolve_date;
    public $client_id, $team_id, $project_id, $assigned_to;
    public $assigned_by, $created_by, $created_at, $updated_by, $updated_at;

    public function render()
    {
        $clients = Client::where('active', true)->get();
        $teams = Team::where('active', true)->get();
        $projects = Project::where('active', true)->get();
        $users = User::where('active', true)->get();

        return view('livewire.issues.form', [
            'clients' => $clients,
            'teams' => $teams,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    #[On('edit')]
    public function edit($id)
    {
        $issue = Issue::find($id);
        if ($issue) {
            $this->model_id = $id;
            $this->description = $issue->description;
            $this->priority = $issue->priority;
            $this->resolve_date = $issue->resolve_date;
            $this->client_id = $issue->client_id;
            $this->team_id = $issue->team_id;
            $this->project_id = $issue->project_id;
            $this->assigned_to = $issue->assigned_to;
            $this->assigned_by = $issue->assigned_by;
            $this->created_by = $issue->created_by;
            $this->created_at = $issue->created_at;
            $this->updated_by = $issue->updated_by;
            $this->updated_at = $issue->updated_at;
        }
    }

    public function save()
    {
        $this->validate([
            'description' => 'required',
            'priority' => 'required',
            'resolve_date' => 'nullable',
            'client_id' => 'nullable|exists:clients,id',
            'team_id' => 'nullable|exists:teams,id',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $data = [
            'description' => $this->description,
            'priority' => $this->priority,
            'resolve_date' => $this->resolve_date,
            'client_id' => $this->client_id,
            'team_id' => $this->team_id,
            'project_id' => $this->project_id,
            'assigned_to' => $this->assigned_to,
            'updated_by' => auth()->id(),
        ];

        if ($this->assigned_to) {
            $data['assigned_by'] = auth()->id();
        }

        if ($this->model_id) {
            Issue::where('id', $this->model_id)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            Issue::create($data);
        }

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Issue saved successfully.');
    }
}
