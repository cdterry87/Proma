@props(['resultsCount', 'filterOptions' => [], 'sortByOptions' => []])

<div>
    <div class="dark:bg-gray-800 p-4 rounded-lg flex flex-col gap-2">
        <h4 class="font-bold text-lg uppercase text-gray-500">Filters and Sorting</h4>
        <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between lg:gap-4">
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex flex-col gap-1">
                    <x-label
                        for="perPage"
                        :value="__('Show')"
                    />
                    <x-select
                        id="perPage"
                        type="text"
                        wire:model.live="perPage"
                        without-default
                    >
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="60">60</option>
                        <option value="120">120</option>
                        <option value="240">240</option>
                    </x-select>
                </div>

                {{ $slot }}

                @if ($sortByOptions)
                    <div class="flex flex-col gap-1">
                        <x-label
                            for="sortBy"
                            :value="__('Sort By')"
                        />
                        <x-select
                            id="sortBy"
                            type="text"
                            wire:model.live="sortBy"
                            without-default
                        >
                            @foreach ($sortByOptions as $value => $description)
                                <option value="{{ $value }}">{{ $description }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <x-label
                            for="sortType"
                            :value="__('Sort Type')"
                        />
                        <x-select
                            id="sortType"
                            type="text"
                            wire:model.live="sortType"
                            without-default
                        >
                            <option value="asc">Asc</option>
                            <option value="desc">Desc</option>
                        </x-select>
                    </div>
                @endif
            </div>
            <div>
                <x-search
                    placeholder="Search..."
                    wire:model.live.debounce.500ms="search"
                />
            </div>
        </div>
    </div>

    <x-hr class="mt-6 mb-2" />
</div>
