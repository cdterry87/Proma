<x-layouts::error
    code="500"
    title="Something went wrong"
    message="An unexpected error occurred. Please try again shortly."
>
    <x-button icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
