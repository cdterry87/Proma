<x-layouts::error
    title="Page not found"
    illustration="not-found"
    message="Sorry, we could not find the page you were looking for."
>
    <x-button variant="primary" icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
