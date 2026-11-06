<x-layouts::error
    code="403"
    title="Access denied"
    message="You do not have permission to view this page."
>
    <x-button icon="arrow-left" href="{{ url('/') }}">Return home</x-button>
</x-layouts::error>
