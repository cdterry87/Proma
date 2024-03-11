@props(['label' => null, 'labelClasses' => '', 'icon' => null])

<button {{ $attributes->merge([
    'class' => 'btn uppercase font-bold',
]) }}>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    <span class="{{ $labelClasses }}">
        {{ $label ?? $slot }}
    </span>
</button>
