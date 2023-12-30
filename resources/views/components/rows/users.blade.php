@props(['results'])

<x-rows.container>
    @if ($results && $results->isNotEmpty())
        @foreach ($results as $result)
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 dark:bg-gray-800 dark:text-gray-300 p-4 rounded-lg">
                <div class="flex items-center gap-4">
                    <img
                        class="h-12 w-12 rounded-full object-cover"
                        src="{{ $result->profile_photo_url }}"
                        alt="{{ $result->name }}"
                    />
                    <div class="flex flex-col">
                        <div class="text-lg font-bold">{{ $result->name }}</div>
                        <div class="text-sm text-gray-400 uppercase">{{ $result->title ?? 'No Title' }}</div>
                        <div class="flex items-center gap-6 text-sm">
                            @if ($result->email)
                                <div class="flex items-center gap-1">
                                    <x-icons.email class="h-4 w-4 text-indigo-600" />
                                    {{ $result->email }}
                                </div>
                            @endif
                            @if ($result->phone)
                                <div class="flex items-center gap-1">
                                    <x-icons.phone class="h-4 w-4 text-indigo-600" />
                                    {{ $result->getFormattedPhoneNumber() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button
                        class="rounded-lg sm:rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out flex items-center gap-1"
                        wire:click.prevent="edit({{ $result->id }})"
                        alt="Edit"
                        title="Edit"
                    >
                        <x-icons.edit class="h-4 w-4 sm:h-6 sm:w-6" />
                        <span class="sm:hidden uppercase">Edit</span>
                    </button>
                    <x-confirms-delete wire:then="delete({{ $result->id }})">
                        <button
                            class="rounded-lg sm:rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out flex items-center gap-1"
                            alt="Delete"
                            title="Delete"
                        >
                            <x-icons.delete class="h-4 w-4 sm:h-6 sm:w-6" />
                            <span class="sm:hidden uppercase">Delete</span>
                        </button>
                    </x-confirms-delete>
                </div>
            </div>
        @endforeach
        <div>
            {{ $results->links() }}
        </div>
    @else
        <x-empty-results />
    @endif
</x-rows.container>
