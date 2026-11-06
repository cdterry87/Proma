@props(['placeholder' => 'Search', 'filters' => [], 'idPrefix' => 'table', 'actions' => null])

<div {{ $attributes->class('table-toolbar') }}>
    <div class="table-toolbar__filters">
        <x-input
            name="search"
            :id="$idPrefix.'-search'"
            type="search"
            label="Search"
            hide-label
            :placeholder="$placeholder"
            wire:model.live.debounce.300ms="search"
        />

        @foreach ($filters as $key => $filter)
            <x-select
                :name="'filters.'.$key"
                :id="$idPrefix.'-filter-'.$key"
                :label="$filter->label"
                hide-label
                wire:model.live="filters.{{ $key }}"
            >
                <option value="">{{ $filter->label }}: All</option>
                @foreach ($filter->options as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-select>
        @endforeach
    </div>

    <div class="cluster">
        <x-button icon="download" wire:click="export('csv')">CSV</x-button>
        <x-button icon="download" wire:click="export('xlsx')">XLSX</x-button>
        {{ $actions }}
    </div>
</div>
