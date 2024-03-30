@props(['title' => null, 'top' => null, 'topActions' => null, 'bottomActions' => null])

<div {{ $attributes->merge([
    'class' => 'card bg-base-300 shadow-xl',
]) }}>
    <div class="card-body">
        <div class="flex items-center justify-between gap-4 mb-2">
            @if ($title)
                <h2 class="card-title text-2xl">{{ $title }}</h2>
            @endif
            @if ($topActions)
                <div>
                    {{ $topActions }}
                </div>
            @endif
        </div>

        {{ $slot }}

        @if ($bottomActions)
            <div class="card-actions justify-end mt-6">
                {{ $bottomActions }}
            </div>
        @endif
    </div>
</div>
