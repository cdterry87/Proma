@props(['label' => null])

<button {{ $attributes->merge([
    'class' => 'btn uppercase font-bold',
]) }}>
    {{ $label ?? $slot }}
</button>
