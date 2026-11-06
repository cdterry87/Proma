@props(['title' => null, 'subtitle' => null, 'actions' => null])

<section {{ $attributes->class('card') }}>
    @if ($title || $actions)
        <header class="card__header">
            <div>
                @if ($title)
                    <h2>{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="card__subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($actions)
                <div class="cluster">{{ $actions }}</div>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
