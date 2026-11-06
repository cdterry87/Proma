<x-layouts::error
    code="404"
    title="Page not found"
    message="Sorry, we could not find the page you were looking for."
>
    <x-button icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
