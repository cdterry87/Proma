<?php

namespace App\Livewire;

use App\Models\UserNotification;
use Livewire\Component;

class Notifications extends Component
{
    public function render()
    {
        return view('livewire.notifications');
    }

    public function markAllAsRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->update(['read' => true]);

        $this->dispatch('pg:eventRefresh-NotificationsTable');
    }
}
