@props(['createdAt', 'updatedAt', 'dateFormat' => 'm/d/Y H:i'])

<div class="font-semibold text-gray-600 text-sm w-full mt-6">
    @if ($createdAt == $updatedAt)
        <div class="flex items-center gap-2">
            Created: {{ $createdAt->format($dateFormat) }}
        </div>
    @else
        <div class="flex items-center gap-2">
            <span>Created: {{ $createdAt->format($dateFormat) }}</span>
            <span>Updated: {{ $updatedAt->format($dateFormat) }}</span>
        </div>
    @endif
</div>
