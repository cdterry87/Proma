@props(['id', 'label', 'icon' => null])

<label
    for="{{ $id }}"
    {{ $attributes->merge([
        'class' => 'btn flex items-center gap-1',
    ]) }}
>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    {{ $label ?? $slot }}
</label>
