@props(['label' => null, 'icon' => null])

<button {{ $attributes->merge([
    'class' => 'btn uppercase font-bold flex items-center gap-1',
]) }}>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    {{ $label ?? $slot }}
</button>
