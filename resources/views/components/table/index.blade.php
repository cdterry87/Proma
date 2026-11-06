@props(['caption' => null])

<div class="table-wrap" wire:loading.class="table-wrap--loading">
    <table {{ $attributes->class('table') }}>
        @if ($caption)
            <caption class="visually-hidden">{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
