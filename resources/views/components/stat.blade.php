@props(['label', 'value', 'hint' => null, 'icon' => null])

<div {{ $attributes->class('stat') }}>
    <div class="stat__label">{{ $label }}</div>
    <div class="stat__value">{{ $value }}</div>
    @if ($icon)
        <x-icon :name="$icon" class="stat__icon" />
    @endif
    @if ($hint)
        <div class="stat__hint">{{ $hint }}</div>
    @endif
</div>
