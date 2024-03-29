@props(['id', 'label' => null, 'labelClasses' => null, 'icon' => null])

<label
    for="{{ $id }}"
    {{ $attributes->merge([
        'class' => 'btn uppercase flex items-center gap-1',
    ]) }}
>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    @if ($label)
        <span class="{{ $labelClasses }}">
            {{ $label ?? $slot }}
        </span>
    @endif
</label>
