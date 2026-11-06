@props(['title', 'message'])

<x-layouts::guest :title="$title">
    <x-card :title="$title">
        <p>{{ $message }}</p>
        {{ $slot }}
    </x-card>
</x-layouts::guest>
