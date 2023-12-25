<?php

namespace App\Livewire;

use App\Models\User;
use App\Livewire\Alert;
use App\Models\Team;
use App\Models\TeamUser;
use Livewire\Component;
use App\Traits\WithDrawer;
use App\Traits\WithSearch;
use App\Traits\ConfirmsDeletes;

class Teams extends Component
{
    use ConfirmsDeletes;
    use WithDrawer;
    use WithSearch;

    // Team form
    public $model_id;
    public $name, $description;
    public $active = true;

    // Team Members form
    public $isAlternateForm = false;
    public $user_id;
    public $manager = false;

    public function rules()
    {
        return $this->isAlternateForm ? [
            'user_id' => 'required|numeric|exists:users,id',
        ] : [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|min:3|max:255',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function render()
    {
        $results = Team::query()
            ->when($this->search && strlen($this->search) >= 3, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->orderBy, function ($query) {
                $query->orderBy($this->orderBy, $this->orderType);
            })
            ->paginate($this->perPage);

        $users = null;
        $members = null;
        if ($this->model_id) {
            $users = User::query()
                ->where('active', true)
                ->whereNotIn('id', function ($query) {
                    $query->select('user_id')
                        ->from('teams_users')
                        ->where('team_id', $this->model_id);
                })
                ->orderBy('name')
                ->get();

            $members = TeamUser::query()
                ->where('team_id', $this->model_id)
                ->orderBy('manager', 'desc')
                ->orderBy('user_id')
                ->get();
        }


        return view('livewire.teams', [
            'results' => $results,
            'users' => $users,
            'members' => $members,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->model_id) {
            Team::find($this->model_id)->update([
                'name' => $this->name,
                'description' => $this->description,
                'active' => $this->active,
                'updated_by' => auth()->user()->id,
            ]);
        } else {
            $result = Team::create([
                'name' => $this->name,
                'description' => $this->description,
                'active' => $this->active,
                'created_by' => $this->model_id ? null : auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            $this->model_id = $result->id;
        }

        $this->showDrawerAlert('Team saved successfully.');
    }

    public function edit($id)
    {
        $result = Team::find($id);
        if (!$result) {
            return;
        }

        $this->model_id = $result->id;
        $this->name = $result->name;
        $this->description = $result->description;

        $this->openDrawer();
    }

    public function delete($id)
    {
        Team::find($id)->delete();

        $this->dispatch('showAlert', 'Team deleted successfully.')
            ->to(Alert::class);
    }

    public function openMembersDrawer($id)
    {
        $this->model_id = $id;
        $this->isAlternateForm = true;
        $this->openDrawer();
    }

    public function closeMembersDrawer()
    {
        $this->isAlternateForm = false;
        $this->closeDrawer();
    }

    public function addMember()
    {
        $this->validate();

        TeamUser::create([
            'team_id' => $this->model_id,
            'user_id' => $this->user_id,
            'manager' => $this->manager,
            'created_by' => auth()->user()->id,
        ]);

        $this->showDrawerAlert('Team Member added successfully.');

        $this->reset(['user_id', 'manager']);
    }

    public function deleteMember($id)
    {
        TeamUser::find($id)->delete();

        $this->showDrawerAlert('Team Member deleted successfully.');
    }
}
