@props(['title' => null, $actions => null])

<div {{ $attributes->merge([
    'class' => 'card shadow-xl',
]) }}>
    <div class="card-body">
        @if ($title)
            <h2 class="card-title">{{ $title }}</h2>
        @endif
        {{ $slot }}
        @if ($actions)
            <div class="card-actions justify-end">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
