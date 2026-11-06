<x-layouts::error
    title="Page expired"
    illustration="forbidden"
    message="This page sat open too long. Please go back and try again."
>
    <x-button variant="primary" icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
