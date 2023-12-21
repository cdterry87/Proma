@props(['title', 'button' => null, 'buttonIcon' => 'icons.plus', 'action' => 'openDrawer'])

<div class="flex items-center justify-between gap-4">
    <h1 class="font-bold text-4xl text-white">{{ $title }}</h1>
    @if ($button)
        <x-button
            wire:click.prevent="{{ $action }}"
            class="flex items-center gap-1"
        >
            {{ $button }}
            @if ($buttonIcon)
                <x-dynamic-component
                    :component="$buttonIcon"
                    class="h-4 w-4"
                />
            @endif
        </x-button>
    @endif
</div>
