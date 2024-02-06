<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UsersForm extends Component
{
    public $active, $name, $email, $password;

    public function render()
    {
        return view('livewire.users-form');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email', // @todo - add unique validation when user is being edited
            'password' => 'required|min:6',
        ]);

        // @todo - add user update logic
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        session()->flash('success', 'User saved successfully.');
    }
}
