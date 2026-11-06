<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarUser extends Component
{
    #[On('profile-updated')]
    public function refresh(): void
    {
        // Receiving the event is enough to render the new profile.
    }

    public function render(): View
    {
        return view('livewire.sidebar-user', ['user' => Auth::user()]);
    }
}
