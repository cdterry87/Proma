<x-layouts::error
    code="419"
    title="Page expired"
    message="This page sat open too long. Please go back and try again."
>
    <x-button icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
