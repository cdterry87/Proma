<div class="page">
    <x-page-header title="Clients">
        <x-slot:actions>
            <x-button variant="primary" icon="plus" dialog="client-form">Add client</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table.toolbar placeholder="Search clients" :filters="$tableFilters" id-prefix="clients" />

    <x-table caption="Clients">
        <thead>
            <tr>
                <x-table.head><span class="visually-hidden">Details</span></x-table.head>
                <x-table.head sortable="name" :sort="$activeSort" :direction="$activeDirection">Name</x-table.head>
                <x-table.head sortable="incomplete_projects_count" :sort="$activeSort" :direction="$activeDirection">Projects</x-table.head>
                <x-table.head sortable="unresolved_issues_count" :sort="$activeSort" :direction="$activeDirection">Issues</x-table.head>
                <x-table.head sortable="contacts_count" :sort="$activeSort" :direction="$activeDirection">Contacts</x-table.head>
                <x-table.head sortable="active" :sort="$activeSort" :direction="$activeDirection">Status</x-table.head>
                <x-table.head sortable="updated_at" :sort="$activeSort" :direction="$activeDirection">Updated</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        @forelse ($rows as $client)
            <x-table.expandable-row colspan="8" :label="'Show description of '.$client->name" :detail="$client->description" wire:key="client-{{ $client->uuid }}">
                <x-table.cell label="Name">
                    <a href="{{ route('clients.show', $client) }}" wire:navigate>{{ $client->name }}</a>
                </x-table.cell>
                <x-table.cell label="Projects">{{ $client->incomplete_projects_count }}</x-table.cell>
                <x-table.cell label="Issues">{{ $client->unresolved_issues_count }}</x-table.cell>
                <x-table.cell label="Contacts">{{ $client->contacts_count }}</x-table.cell>
                <x-table.cell label="Status">
                    <x-badge :variant="$client->active ? 'success' : null">{{ $client->active ? 'Active' : 'Inactive' }}</x-badge>
                </x-table.cell>
                <x-table.cell label="Updated" class="table__cell--nowrap">{{ $client->updated_at->diffForHumans() }}</x-table.cell>
                <x-table.cell>
                    <div class="table__actions">
                        <x-button variant="ghost" size="sm" icon="eye" :label="'View '.$client->name" :href="route('clients.show', $client)" wire:navigate />
                    </div>
                </x-table.cell>
            </x-table.expandable-row>
        @empty
            <tbody>
                <x-table.empty colspan="8">No clients found.</x-table.empty>
            </tbody>
        @endforelse
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="clients" />

    <x-dialog name="client-form" title="Add client">
        @include('livewire.clients.form-fields')
    </x-dialog>
</div>
