<div>
    <div class="w-full">
        <div class="bg-indigo-600 text-gray-100 flex items-center justify-between gap-4 p-4 rounded-t-xl">
            <h1 class="font-bold text-2xl">Manage Users</h1>
            <x-icons.plus class="h-8 w-8" />
        </div>
        <div
            class="flex flex-col divide-y p-4 rounded-b-xl divide-gray-700 bg-base-200 text-gray-900 dark:bg-gray-800 dark:text-gray-100">
            @foreach ($results as $result)
                <div class="flex flex-wrap items-center gap-6 py-4">
                    <img
                        class="h-12 w-12 rounded-full object-cover"
                        src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}"
                    />
                    <div class="flex flex-col gap-1 flex-1">
                        <div class="font-bold text-xl">{{ $result->name }}</div>
                        <div class="flex items-center gap-2">
                            <div class="font-semibold text-sm flex items-center gap-1">
                                <x-icons.email class="h-4 w-4" />
                                {{ $result->email }}
                            </div>
                            @if ($result->phone)
                                <span>|</span>
                                <div class="font-semibold text-sm flex items-center gap-1">
                                    <x-icons.phone class="h-4 w-4" />
                                    <span>
                                        {{ $result->phone }}
                                        {{ $result->phone_ext ? ' Ext. ' . $result->phone_ext : '' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <x-button>
                            View
                            <x-icons.chevron-right class="h-4 w-4" />
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
