<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Settings extends Component
{
    public $user;
    public $name, $email, $title, $password, $password_confirmation;

    public function mount()
    {
        $this->user = auth()->user();
        if ($this->user) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->title = $this->user->title;
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }

    public function saveName()
    {
        $this->validate([
            'name' => 'required|min:6|max:255',
        ]);

        auth()->user()->update([
            'name' => $this->name
        ]);

        session()->flash('success', 'Name saved successfully.');

        // Refresh user
        $this->user = auth()->user();
        $this->dispatch('refreshUser');
    }

    public function saveEmail()
    {
        $this->validate([
            'email' => 'required|max:255|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'email' => $this->email
        ]);

        session()->flash('success', 'Email saved successfully.');

        // Refresh user
        $this->user = auth()->user();
        $this->dispatch('refreshUser');
    }

    public function saveTitle()
    {
        $this->validate([
            'title' => 'nullable|max:255',
        ]);

        auth()->user()->update([
            'title' => $this->title
        ]);

        session()->flash('success', 'Title saved successfully.');

        // Refresh user
        $this->user = auth()->user();
        $this->dispatch('refreshUser');
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

        // Refresh user
        $this->user = auth()->user();
        $this->dispatch('refreshUser');
    }
}
