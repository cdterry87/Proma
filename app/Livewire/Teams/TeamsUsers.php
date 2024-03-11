<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Traits\WithModal;
use Livewire\Component;
use Livewire\Attributes\On;

class TeamsUsers extends Component
{
    use WithModal;

    public $team_id, $team_name;
    public $user_id;

    #[On('edit')]
    public function edit($id)
    {
        $team = Team::find($id);
        if ($team) {
            $this->team_id = $team->id;
            $this->team_name = $team->name;
        }
    }

    public function render()
    {
        $users = User::query()
            ->where('active', true)
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')
                    ->from('teams_users')
                    ->where('team_id', $this->team_id);
            })
            ->get();

        return view('livewire.teams.teams-users', [
            'users' => $users,
        ]);
    }

    public function addMember()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        TeamUser::create([
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'created_by' => auth()->id(),
        ]);

        $this->user_id = null;

        $this->dispatch('refreshData');
    }

    #[On('removeMember')]
    public function removeMember($userId, $teamId)
    {
        $team = TeamUser::query()
            ->where('user_id', $userId)
            ->where('team_id', $teamId)
            ->first();

        if ($team) {
            $team->delete();
        }

        $this->dispatch('refreshData');
    }
}
