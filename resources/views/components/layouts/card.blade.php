@props(['title' => null, 'subtitle' => null, 'top' => null, 'topActions' => null, 'bottomActions' => null])

<div {{ $attributes->merge([
    'class' => 'card bg-base-200 shadow-xl',
]) }}>
    <div class="card-body">
        @if ($title || $subtitle)
            <div class="flex flex-col">
                @if ($title || $topActions)
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
                @endif
                @if ($subtitle)
                    <h3 class="font-semibold text-md text-gray-600 dark:text-gray-400">{{ $subtitle }}</h3>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($bottomActions)
            <div class="card-actions justify-end mt-6">
                {{ $bottomActions }}
            </div>
        @endif
    </div>
</div>
