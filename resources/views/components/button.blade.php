@props([
    'variant' => 'neutral',
    'size' => null,
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'block' => false,
    'label' => null,
    'dialog' => null,
])

@php
    $classes = [
        'btn',
        'btn--'.$variant,
        'btn--sm' => $size === 'sm',
        'btn--icon' => $slot->isEmpty(),
        'btn--block' => $block,
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-icon :name="$icon" :size="$size" />
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        aria-label="{{ $label }}"
        @if ($dialog) data-open-dialog="{{ $dialog }}" @endif
        {{ $attributes->class($classes) }}
    >
        @if ($icon)
            <x-icon :name="$icon" :size="$size" />
        @endif
        {{ $slot }}
    </button>
@endif
