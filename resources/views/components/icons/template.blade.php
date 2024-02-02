<svg
    {{ $attributes->merge([
        'class' => 'stroke-current shrink-0 h-6 w-6',
        'fill' => 'none',
        'viewBox' => '0 0 24 24',
        'stroke' => 'currentColor',
        'stroke-width' => '1.5',
    ]) }}>
    {{ $slot }}
</svg>
