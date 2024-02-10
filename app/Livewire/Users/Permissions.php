<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use App\Traits\WithModal;
use Livewire\Attributes\On;

class Permissions extends Component
{
    use WithModal;

    public $user_id, $user_name;

    public function render()
    {
        return view('livewire.users.permissions');
    }

    #[On('edit')]
    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->user_id = $user->id;
            $this->user_name = $user->name;
        }
    }

    public function save()
    {
        session()->flash('success', 'Permissions saved successfully.');
    }
}
