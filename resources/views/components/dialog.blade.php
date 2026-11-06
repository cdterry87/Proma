@props(['name', 'title', 'subtitle' => null])

<dialog
    wire:ignore.self
    x-data="dialog('{{ $name }}')"
    x-on:click="closeOnBackdrop"
    aria-labelledby="{{ $name }}-title"
    {{ $attributes->class('dialog') }}
>
    <div class="dialog__panel">
        <header class="dialog__header">
            <div>
                <h2 id="{{ $name }}-title">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="dialog__subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            <x-button variant="ghost" size="sm" icon="x" label="Close" data-close-dialog />
        </header>

        {{ $slot }}
    </div>
</dialog>
