<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class SideBarUserDetails extends Component
{
    public $name, $title;

    public function mount()
    {
        $this->getUserDetails();
    }

    public function render()
    {
        return view('livewire.side-bar-user-details');
    }

    #[On('refreshUser')]
    public function getUserDetails()
    {
        $user = auth()->user();

        if ($user) {
            $this->name = $user->name;
            $this->title = $user->title;
        }
    }
}
