@props([
    'title',
    'primaryLabel' => 'Save',
    'primaryMethod' => 'save',
    'secondaryLabel' => 'Cancel',
    'secondaryMethod' => 'closeDrawer',
])

<div class="flex flex-col gap-4">
    {{-- Drawer Header --}}
    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">{{ $title }}</h2>
        <div class="flex items-center gap-4">
            <x-button wire:click.prevent="{{ $primaryMethod }}">
                {{ $primaryLabel }}
            </x-button>
            <x-button wire:click.prevent="{{ $secondaryMethod }}">
                {{ $secondaryLabel }}
            </x-button>
        </div>
    </div>

    {{-- Drawer Alert --}}
    @if (session()->has('drawer-alert--message'))
        <div
            x-data
            x-init="setTimeout(() => {
                $dispatch('hideDrawerAlert')
            }, 10000)"
            class="relative w-full py-4 pr-10 pl-4 bg-indigo-600 text-white rounded-lg"
        >
            <p>
                {{ session('drawer-alert--message') }}
            </p>
            <button
                class="absolute right-4 top-4"
                wire:click.prevent="hideDrawerAlert"
            >
                <x-icons.close class="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- Drawer Content --}}
    <div>
        {{ $slot }}
    </div>
</div>
