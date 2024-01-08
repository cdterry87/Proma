<div class="flex flex-col gap-4">
    {{-- Header --}}
    <x-header
        title="Manage Teams"
        button="Add Team"
    />

    {{-- Filter, Search, Sort --}}
    <x-filters :sort-by-options="$sortByOptions">
        <div class="flex flex-col gap-1">
            <x-label
                for="filters.active"
                :value="__('Status')"
            />
            <x-select
                id="filters.active"
                type="text"
                wire:model.live="filters.active"
                without-default
            >
                <option value="">All</option>
                <option value="A">Active</option>
                <option value="I">Inactive</option>
            </x-select>
        </div>
    </x-filters>

    {{-- Content --}}
    <x-rows.teams :results="$results" />

    {{-- Drawers --}}
    @if ($isAlternateForm)
        <x-forms.teams-members
            :users="$users"
            :members="$members"
        />
    @else
        <x-forms.teams />
    @endif
</div>
