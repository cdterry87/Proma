<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Privacy Policy')]
class PrivacyPolicy extends Component
{
    public function render(): View
    {
        return view('livewire.privacy-policy')
            ->layout(Auth::check() ? 'layouts::app' : 'layouts::guest', ['wide' => true]);
    }
}
