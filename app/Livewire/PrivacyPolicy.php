<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PrivacyPolicy extends Component
{
    public function render()
    {
        $layout = Auth::check() ? 'components.layouts.app' : 'components.layouts.guest';

        return view('livewire.privacy-policy')
            ->layout($layout);
    }
}
