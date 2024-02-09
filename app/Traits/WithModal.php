<?php

namespace App\Traits;

use Livewire\Attributes\On;

trait WithModal
{
    #[On('closeModal')]
    public function closeModal()
    {
        $this->reset();
    }
}
