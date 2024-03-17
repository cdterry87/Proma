<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use App\Models\TeamUser;
use App\Models\TeamUpload;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class TeamsView extends Component
{
    public $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        $teamUsers = $this->team->users->sortBy('name');

        return view('livewire.teams.teams-view', [
            'teamUsers' => $teamUsers,
            'teamUsersActiveCount' => $teamUsers->where('active', true)->count(),
            'teamUsersInactiveCount' => $teamUsers->where('active', false)->count(),
        ]);
    }

    #[On('refreshData')]
    public function getTeam()
    {
        $this->team = Team::find($this->team->id);
    }

    #[On('download')]
    public function download($id)
    {
        $file = TeamUpload::query()
            ->where('team_id', $this->team->id)
            ->where('id', $id)
            ->first();

        if ($file) {
            return Storage::download($file->path, $file->name);
        }
    }
}
