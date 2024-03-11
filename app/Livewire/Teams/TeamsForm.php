<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class TeamsForm extends Component
{
    use WithModal;

    public $model_id;
    public $active = true;
    public $name, $description;

    public function render()
    {
        return view('livewire.teams.teams-form');
    }

    #[On('edit')]
    public function edit($id)
    {
        $team = Team::find($id);
        if ($team) {
            $this->model_id = $id;
            $this->active = !!$team->active;
            $this->name = $team->name;
            $this->description = $team->description;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

        $data = [
            'active' => $this->active,
            'name' => $this->name,
            'description' => $this->description,
            'updated_by' => auth()->id(),
        ];

        if ($this->model_id) {
            Team::where('id', $this->model_id)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            Team::create($data);
        }

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Team saved successfully.');
    }
}
