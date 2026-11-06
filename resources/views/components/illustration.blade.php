@props(['name'])

<svg
    {{ $attributes->class('illustration') }}
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 200 140"
    fill="none"
    aria-hidden="true"
>
    @include('illustrations.'.$name)
</svg>
