@props(['caption' => null])

<div class="table-wrap">
    <table {{ $attributes->class('table') }}>
        @if ($caption)
            <caption class="visually-hidden">{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
