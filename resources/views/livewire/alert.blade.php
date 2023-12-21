<div
    id="alert--container"
    x-data
    x-init="setTimeout(() => {
        $dispatch('hideAlert')
    }, 10000)"
>
    @if ($message)
        <div class="fixed bottom-0 right-0 m-6">
            <div class="bg-white dark:bg-indigo-600 rounded-lg">
                <div class="flex items-center justify-between gap-4">
                    <p class="py-4 px-4 text-gray-800 dark:text-white text-base">
                        {{ $message }}
                    </p>
                    <button
                        class="p-4"
                        wire:click.prevent="hideAlert"
                    >
                        <x-icons.close class="h-4 w-4 text-gray-800 dark:text-white" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
