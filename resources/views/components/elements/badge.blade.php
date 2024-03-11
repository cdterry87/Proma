@props(['label'])

<div {{ $attributes->merge([
    'class' => 'badge font-bold',
]) }}>
    {{ $label ?? $slot }}
</div>
