@props(['name', 'title', 'action', 'confirmLabel' => 'Delete'])

<x-dialog :name="$name" :title="$title">
    <p>{{ $slot }}</p>

    <div class="form__actions">
        <x-button variant="ghost" data-close-dialog>Cancel</x-button>
        <x-button variant="danger-solid" wire:click="{{ $action }}" data-close-dialog>
            {{ $confirmLabel }}
        </x-button>
    </div>
</x-dialog>
