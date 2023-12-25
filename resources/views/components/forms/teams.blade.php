@props(['managers'])

<x-drawer>
    <x-drawer-form-container title="Team Form">
        <form class="flex flex-col gap-4">
            <div class="flex justify-end">
                <x-toggle
                    label="Active"
                    wire:model="active"
                />
            </div>

            <div class="flex flex-col gap-1">
                <x-label
                    for="name"
                    :value="__('Name')"
                />
                <x-input
                    id="name"
                    class="w-full"
                    type="text"
                    wire:model="name"
                    autofocus
                />
                <x-input-error for="name" />
            </div>

            <div class="flex flex-col gap-1">
                <x-label
                    for="description"
                    :value="__('Description')"
                />
                <x-textarea
                    id="description"
                    class="w-full"
                    type="text"
                    wire:model="description"
                />
                <x-input-error for="description" />
            </div>
        </form>
    </x-drawer-form-container>
</x-drawer>
