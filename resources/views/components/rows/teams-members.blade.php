@props(['results'])

<x-rows.container>
    @if ($results->isNotEmpty())
        @foreach ($results as $result)
            <div class="flex items-center justify-between gap-2 px-4 py-2 bg-gray-700 rounded-lg">
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span>
                            {{ $result->user->name }}
                        </span>
                        @if ($result->manager)
                            <span class="text-gray-400">(Manager)</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 uppercase">{{ $result->user->title ?? 'No Title' }}</div>
                </div>
                <div>
                    <x-confirms-delete wire:then="deleteMember({{ $result->id }})">
                        <button
                            class="rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out"
                            alt="Delete"
                            title="Delete"
                        >
                            <x-icons.delete class="h-6 w-6" />
                        </button>
                    </x-confirms-delete>
                </div>
            </div>
        @endforeach
    @else
        <x-empty-results
            title="No members found."
            description="Use the form above to add a member."
        />
    @endif
</x-rows.container>
