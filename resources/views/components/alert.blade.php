@props(['variant' => 'info'])

@php
    $icons = ['info' => 'info', 'success' => 'success', 'warning' => 'warning', 'error' => 'error'];
@endphp

<div
    role="{{ in_array($variant, ['error', 'warning'], true) ? 'alert' : 'status' }}"
    {{ $attributes->class(['alert', 'alert--'.$variant]) }}
>
    <x-icon :name="$icons[$variant]" />
    <div class="alert__body">{{ $slot }}</div>
</div>
