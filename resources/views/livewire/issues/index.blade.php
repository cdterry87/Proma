@use('App\Support\DateFormatter')

<div class="page">
    <x-page-header title="Issues">
        <x-slot:actions>
            <x-button variant="primary" icon="plus" dialog="issue-form">Add issue</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table.toolbar placeholder="Search issues" :filters="$tableFilters" id-prefix="issues" />

    <x-table caption="Issues">
        <thead>
            <tr>
                <x-table.head><span class="visually-hidden">Details</span></x-table.head>
                <x-table.head sortable="name" :sort="$activeSort" :direction="$activeDirection">Name</x-table.head>
                <x-table.head sortable="priority" :sort="$activeSort" :direction="$activeDirection">Priority</x-table.head>
                <x-table.head sortable="client_name" :sort="$activeSort" :direction="$activeDirection">Client</x-table.head>
                <x-table.head sortable="project_name" :sort="$activeSort" :direction="$activeDirection">Project</x-table.head>
                <x-table.head sortable="resolved_date" :sort="$activeSort" :direction="$activeDirection">Resolved</x-table.head>
                <x-table.head sortable="updated_at" :sort="$activeSort" :direction="$activeDirection">Updated</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        @forelse ($rows as $issue)
            <x-table.expandable-row colspan="8" :label="'Show description of '.$issue->name" :detail="$issue->description" wire:key="issue-{{ $issue->id }}">
                <x-table.cell label="Name">
                    <a href="{{ route('issues.show', $issue) }}" wire:navigate>{{ $issue->name }}</a>
                </x-table.cell>
                <x-table.cell label="Priority">
                    <x-badge :variant="$issue->priority->badgeVariant()">{{ $issue->priority->label() }}</x-badge>
                </x-table.cell>
                <x-table.cell label="Client">{{ $issue->client->name }}</x-table.cell>
                <x-table.cell label="Project">{{ $issue->project?->name ?? 'None' }}</x-table.cell>
                <x-table.cell label="Resolved" class="table__cell--nowrap">{{ DateFormatter::format($issue->resolved_date, 'Unresolved') }}</x-table.cell>
                <x-table.cell label="Updated" class="table__cell--nowrap">{{ $issue->updated_at->diffForHumans() }}</x-table.cell>
                <x-table.cell>
                    <div class="table__actions">
                        <x-button variant="ghost" size="sm" icon="eye" :label="'View '.$issue->name" :href="route('issues.show', $issue)" wire:navigate />
                    </div>
                </x-table.cell>
            </x-table.expandable-row>
        @empty
            <tbody>
                <x-table.empty colspan="8">No issues found.</x-table.empty>
            </tbody>
        @endforelse
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="issues" />

    <x-dialog name="issue-form" title="Add issue">
        @if ($clientOptions->isEmpty())
            <p>You need an active client first. <a href="{{ route('clients.index') }}" wire:navigate>Add a client</a>.</p>
        @else
            @include('livewire.issues.form-fields')
        @endif
    </x-dialog>
</div>
