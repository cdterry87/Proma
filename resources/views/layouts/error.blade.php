@props(['title', 'message', 'illustration'])

<x-layouts::guest :title="$title">
    <x-card class="error-page">
        <x-illustration :name="$illustration" />
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        {{ $slot }}
    </x-card>
</x-layouts::guest>
