<div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between lg:gap-4">
    <div class="flex items-center gap-2">
        <x-button class="flex items-center gap-1">
            Filter
            <x-icons.filter class="h-4 w-4" />
        </x-button>
        <x-button class="flex items-center gap-1">
            Sort
            <x-icons.sort class="h-4 w-4" />
        </x-button>
    </div>
    <div>
        <x-search
            placeholder="Search..."
            wire:model.debounce.500ms="search"
        />
    </div>
</div>