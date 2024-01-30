@props(['icon' => null])

<div {{ $attributes->merge([
    'class' => 'alert flex items-center gap-2',
]) }}>
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    {{ $slot }}
</div>
