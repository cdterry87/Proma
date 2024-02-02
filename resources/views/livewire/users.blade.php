<div class="w-full max-w-6xl mx-auto">
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="font-bold text-3xl">Users</h1>
            <x-inputs.button
                label="Add User"
                icon="plus"
                class="btn-primary btn-sm"
                wire:click.prevent="addUser"
            />
        </div>

        <x-alerts.container />

        <livewire:users-table />
    </div>
</div>
