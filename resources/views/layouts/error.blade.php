@props(['code', 'title', 'message'])

<x-layouts::guest :title="$title">
    <x-card class="error-page">
        <p class="error-page__code">{{ $code }}</p>
        <h1 class="guest__title">{{ $title }}</h1>
        <p class="muted">{{ $message }}</p>
        {{ $slot }}
    </x-card>
</x-layouts::guest>
