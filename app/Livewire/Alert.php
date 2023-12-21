<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Alert extends Component
{
    public $message;

    public function render()
    {
        return view('livewire.alert');
    }

    #[On('showAlert')]
    public function showAlert($message)
    {
        $this->message = $message;
    }

    #[On('hideAlert')]
    public function hideAlert()
    {
        $this->message = null;
        session()->forget('alert--message');
    }
}
