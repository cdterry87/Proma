<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\HasCurrentUser;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBadge extends Component
{
    use HasCurrentUser;

    #[On('notifications-read')]
    public function refresh(): void
    {
        // Receiving the event is enough to render the new count.
    }

    public function render(): View
    {
        return view('livewire.notification-badge', [
            'count' => $this->currentUser()->userNotifications()->unread()->count(),
        ]);
    }
}
