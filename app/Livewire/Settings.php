<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Settings extends Component
{
    public $name, $email, $password, $password_confirmation;

    public function render()
    {
        $user = auth()->user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
        }

        return view('livewire.settings', [
            'user' => auth()->user()
        ]);
    }

    public function saveName()
    {
        $this->validate([
            'name' => 'required|min:6',
        ]);

        auth()->user()->update([
            'name' => $this->name
        ]);

        session()->flash('success', 'Name saved successfully.');
    }

    public function saveEmail()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'email' => $this->email
        ]);

        session()->flash('success', 'Email saved successfully.');
    }

    public function changePassword()
    {
        $this->validate([
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password)
        ]);

        session()->flash('success', 'Password changed successfully.');
    }
}
