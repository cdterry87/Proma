<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class IssuesForm extends Component
{
    use WithModal;

    public $model_id, $user_id;
    public $name, $description, $priority, $resolved_date;
    public $client_id, $team_id, $project_id;
    public $created_at, $updated_at;

    public function render()
    {
        $clients = Client::query()
            ->where('user_id', auth()->id())
            ->where('active', true)
            ->get();
        $projects = Project::query()
            ->where('user_id', auth()->id())
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->whereNull('completed_date')
            ->get();

        return view('livewire.issues.issues-form', [
            'clients' => $clients,
            'projects' => $projects,
            'priorityCodes' => Issue::getPriorityCodes(),
        ]);
    }

    #[On('edit')]
    public function edit($id)
    {
        $issue = Issue::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        if ($issue) {
            $this->model_id = $id;
            $this->user_id = $issue->user_id;
            $this->name = $issue->name;
            $this->description = $issue->description;
            $this->priority = $issue->priority;
            $this->resolved_date = $issue->resolved_date ?? null;
            $this->client_id = $issue->client_id;
            $this->project_id = $issue->project_id;
            $this->created_at = $issue->created_at;
            $this->updated_at = $issue->updated_at;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'priority' => 'required',
            'resolved_date' => 'nullable',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        Issue::updateOrCreate([
            'id' => $this->model_id,
        ], [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'resolved_date' => $this->resolved_date,
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
        ]);

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Issue saved successfully.');
    }

    public function toggleResolveIssue()
    {
        $issue = Issue::findOrFail($this->model_id);
        if ($issue->resolved_date) {
            $issue->resolved_date = null;
            session()->flash('success', 'Issue unresolved successfully.');
        } else {
            $issue->resolved_date = now();
            session()->flash('success', 'Issue resolved successfully.');
        }
        $issue->save();

        // Reset resolved date
        $this->resolved_date = $issue->resolved_date;
    }
}
