@props([
    'title' => __('Confirm Delete'),
    'content' => __('Are you sure you want to delete this record?'),
    'button' => __('Confirm'),
])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingDelete('{{ $confirmableId }}')"
    x-on:delete-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
    <x-dialog-modal wire:model.live="confirmingDelete">
        <x-slot name="title">
            {{ $title }}
        </x-slot>

        <x-slot name="content">
            {{ $content }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button
                wire:click="stopConfirmingDelete"
                wire:loading.attr="disabled"
            >
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button
                class="ms-3"
                dusk="confirm-password-button"
                wire:click="confirmDelete"
                wire:loading.attr="disabled"
            >
                {{ $button }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
@endonce
