<div class="page">
    <x-page-header title="Notifications">
        <x-slot:actions>
            <x-button icon="mail-read" wire:click="markAllRead" :disabled="! $hasUnread">Mark all as read</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table.toolbar placeholder="Search notifications" :filters="$tableFilters" id-prefix="notifications" />

    <x-table caption="Notifications">
        <thead>
            <tr>
                <x-table.head><span class="visually-hidden">Details</span></x-table.head>
                <x-table.head sortable="title" :sort="$activeSort" :direction="$activeDirection">Subject</x-table.head>
                <x-table.head sortable="created_at" :sort="$activeSort" :direction="$activeDirection">Received</x-table.head>
                <x-table.head sortable="read_at" :sort="$activeSort" :direction="$activeDirection">Read</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        @forelse ($rows as $notification)
            <x-table.expandable-row
                colspan="5"
                :label="'Show message for '.$notification->title"
                :detail="$notification->message"
                :toggle-action="$notification->isRead() ? null : 'markRead(\''.$notification->uuid.'\')'"
                wire:key="notification-{{ $notification->uuid }}"
            >
                <x-table.cell label="Subject">{{ $notification->title }}</x-table.cell>
                <x-table.cell label="Received" class="table__cell--nowrap">{{ $notification->created_at->diffForHumans() }}</x-table.cell>
                <x-table.cell label="Read">
                    <x-badge :variant="$notification->isRead() ? null : 'info'">{{ $notification->isRead() ? 'Read' : 'Unread' }}</x-badge>
                </x-table.cell>
                <x-table.cell>
                    <div class="table__actions">
                        @if ($notification->subject)
                            <x-button variant="ghost" size="sm" icon="eye" :label="'View record for '.$notification->title" :href="$notification->subject->url()" wire:navigate />
                        @endif
                    </div>
                </x-table.cell>
            </x-table.expandable-row>
        @empty
            <tbody>
                <x-table.empty colspan="5">No notifications found.</x-table.empty>
            </tbody>
        @endforelse
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="notifications" />
</div>
