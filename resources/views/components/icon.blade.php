@props(['name', 'size' => null])

<svg
    {{ $attributes->class(['icon', 'icon--sm' => $size === 'sm']) }}
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    aria-hidden="true"
>
    @include('icons.'.$name)
</svg>
