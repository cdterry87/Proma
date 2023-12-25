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
