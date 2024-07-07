<x-layouts.page title="Notifications">
    <x-slot:action-button>
        <x-inputs.button
            icon="mail-read"
            label="Mark All As Read"
            class="btn-sm btn-primary"
            wire:click.prevent="markAllAsRead"
        />
    </x-slot:action-button>
    <livewire:notifications-table />
</x-layouts.page>
