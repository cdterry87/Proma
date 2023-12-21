<div class="flex flex-col gap-4">
    {{-- Header --}}
    <x-bars.header
        title="Manage Teams"
        button="Add Team"
    />

    {{-- Filter, Search, Sort --}}
    <x-bars.filters />

    {{-- Content --}}
    <x-tables.teams :results="$results" />

    {{-- Drawer --}}
    <x-forms.teams
        :id="$model_id"
        :managers="$managers"
    />
</div>
