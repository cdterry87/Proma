<x-layouts::error
    title="Access denied"
    illustration="forbidden"
    message="You do not have permission to view this page."
>
    <x-button variant="primary" icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
