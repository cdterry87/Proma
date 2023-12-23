<?php

namespace App\Livewire;

use App\Models\User;
use App\Livewire\Alert;
use App\Models\Team;
use Livewire\Component;
use App\Traits\WithDrawer;
use App\Traits\WithSearch;
use App\Traits\ConfirmsDeletes;

class Teams extends Component
{
    use ConfirmsDeletes;
    use WithDrawer;
    use WithSearch;

    public $model_id;
    public $name, $description, $manager_id;
    public $active = true;

    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|min:3|max:255',
            'manager_id' => 'required|numeric|exists:users,id',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function render()
    {
        $managers = User::query()
            ->orderBy('name')
            ->get();

        $results = Team::query()
            ->when($this->search && strlen($this->search) >= 3, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->orderBy, function ($query) {
                $query->orderBy($this->orderBy, $this->orderType);
            })
            ->paginate($this->perPage);

        return view('livewire.teams', [
            'managers' => $managers,
            'results' => $results,
        ]);
    }

    public function save()
    {
        $this->validate();

        if ($this->model_id) {
            Team::find($this->model_id)->update([
                'name' => $this->name,
                'description' => $this->description,
                'manager_id' => $this->manager_id,
                'active' => $this->active,
                'updated_by' => auth()->user()->id,
            ]);
        } else {
            $result = Team::create([
                'name' => $this->name,
                'description' => $this->description,
                'manager_id' => $this->manager_id,
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
        $this->manager_id = $result->manager_id;

        $this->openDrawer();
    }

    public function delete($id)
    {
        Team::find($id)->delete();

        $this->dispatch('showAlert', 'Team deleted successfully.')
            ->to(Alert::class);
    }
}
