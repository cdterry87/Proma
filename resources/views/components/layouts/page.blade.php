@props(['title', 'actionButton' => null])

<div class="w-full max-w-6xl mx-auto">
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="font-bold text-3xl">Users</h1>

            @if ($actionButton)
                {{ $actionButton }}
            @endif
        </div>

        <x-alerts.container />

        {{ $slot }}
    </div>
</div>
