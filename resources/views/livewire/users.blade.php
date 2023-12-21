<div class="flex flex-col gap-4">
    {{-- Header --}}
    <x-bars.header
        title="Manage Users"
        button="Add User"
        button-icon="icons.plus"
        action="openDrawer"
    />

    {{-- Filter, Search, Sort --}}
    <x-bars.filters />

    {{-- Content --}}
    <x-tables.users :results="$results" />

    {{-- Drawer --}}
    <x-forms.users :id="$model_id" />
</div>
