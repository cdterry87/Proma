<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use App\Livewire\Concerns\HasCurrentUser;
use App\Livewire\Concerns\WithDataTable;
use App\Models\Task;
use App\Models\UserNotification;
use App\Support\TableFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notifications')]
class Index extends Component
{
    use HasCurrentUser;
    use WithDataTable;

    /**
     * Notifications read while this page is open. They stay in the Unread
     * filter, so that expanding one does not remove it from under the reader.
     *
     * @var list<int>
     */
    #[Locked]
    public array $readHere = [];

    public function markRead(int $id): void
    {
        $notification = UserNotification::query()->findOrFail($id);

        Gate::authorize('update', $notification);

        if ($notification->isRead()) {
            return;
        }

        $notification->update(['read_at' => now()]);

        $this->readHere[] = $notification->id;
        $this->dispatch('notifications-read');
    }

    public function markAllRead(): void
    {
        $this->currentUser()->userNotifications()->unread()->update(['read_at' => now()]);

        $this->readHere = [];
        $this->dispatch('notifications-read');
        $this->dispatch('toast', type: 'success', message: 'All notifications marked as read.');
    }

    public function render(): View
    {
        return view('livewire.notifications.index', [
            ...$this->tableData(),
            'hasUnread' => $this->currentUser()->userNotifications()->unread()->exists(),
        ]);
    }

    /**
     * @return Builder<UserNotification>
     */
    protected function query(): Builder
    {
        return UserNotification::query()
            ->ownedBy($this->currentUser())
            ->with(['subject' => fn (MorphTo $morph) => $morph->morphWith([Task::class => ['taskable']])]);
    }

    protected function searchColumns(): array
    {
        return ['title', 'message'];
    }

    protected function sortColumns(): array
    {
        return ['title', 'created_at', 'read_at'];
    }

    protected function defaultSort(): string
    {
        return 'created_at';
    }

    protected function defaultDirection(): string
    {
        return 'desc';
    }

    /**
     * @return array<string, TableFilter>
     */
    protected function tableFilters(): array
    {
        return [
            'read' => new TableFilter(
                'Read',
                ['no' => 'Unread', 'yes' => 'Read'],
                fn (Builder $query, string $value) => $value === 'yes'
                    ? $query->whereNotNull('read_at')
                    : $query->whereNull('read_at')->orWhereIn('id', $this->readHere),
            ),
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Subject' => fn (UserNotification $notification) => $notification->title,
            'Message' => fn (UserNotification $notification) => $notification->message,
            'Received' => fn (UserNotification $notification) => $notification->created_at?->toDateTimeString(),
            'Read' => fn (UserNotification $notification) => $notification->isRead() ? 'Yes' : 'No',
        ];
    }

    protected function exportName(): string
    {
        return 'notifications';
    }
}
