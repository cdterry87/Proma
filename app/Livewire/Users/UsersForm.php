<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;

class UsersForm extends Component
{
    use WithModal;

    public $model_id;
    public $active = true;
    public $name, $email, $title, $password;

    public function render()
    {
        return view('livewire.users.users-form');
    }

    #[On('edit')]
    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->model_id = $id;
            $this->active = !!$user->active;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->title = $user->title;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,id,' . $this->model_id,
            'password' => 'required_if:!modelId,true',
        ]);

        $data = [
            'active' => $this->active,
            'name' => $this->name,
            'email' => $this->email,
            'title' => $this->title,
            'updated_by' => auth()->id(),
        ];

        if ($this->model_id) {
            User::where('id', $this->model_id)->update($data);
        } else {
            $data['password'] = Hash::make($this->password);
            $data['created_by'] = auth()->id();
            User::create($data);
        }

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'User saved successfully.');
    }
}
