<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use Livewire\Attributes\On;

class View extends Component
{
    public $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        $teamUsers = $this->team->users->sortBy('name');

        return view('livewire.teams.view', [
            'teamUsers' => $teamUsers,
        ]);
    }

    #[On('refreshData')]
    public function getTeam()
    {
        $this->team = Team::find($this->team->id);
    }
}
