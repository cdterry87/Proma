<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;

class UsersView extends Component
{
    public $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $userPermissions = $this->user->permissions->groupBy('section');

        return view('livewire.users.users-view', [
            'userPermissions' => $userPermissions,
        ]);
    }

    #[On('refreshData')]
    public function getUser()
    {
        $this->user = User::find($this->user->id);
    }

    public function resetPassword()
    {
        // Generate a random 8 character alphanumeric password
        $password = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);

        // Update the user's password
        $this->user->update([
            'password' => Hash::make($password),
        ]);

        // Display a message
        session()->flash('success', 'The user\'s password has been reset to: ' . $password);
    }
}
