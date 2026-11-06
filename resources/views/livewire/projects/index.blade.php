@use('App\Support\DateFormatter')

<div class="page">
    <x-page-header title="Projects">
        <x-slot:actions>
            <x-button variant="primary" icon="plus" dialog="project-form">Add project</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-table.toolbar placeholder="Search projects" :filters="$tableFilters" id-prefix="projects" />

    <x-table caption="Projects">
        <thead>
            <tr>
                <x-table.head><span class="visually-hidden">Details</span></x-table.head>
                <x-table.head sortable="name" :sort="$activeSort" :direction="$activeDirection">Name</x-table.head>
                <x-table.head sortable="client_name" :sort="$activeSort" :direction="$activeDirection">Client</x-table.head>
                <x-table.head sortable="start_date" :sort="$activeSort" :direction="$activeDirection">Start</x-table.head>
                <x-table.head sortable="due_date" :sort="$activeSort" :direction="$activeDirection">Due</x-table.head>
                <x-table.head sortable="completed_date" :sort="$activeSort" :direction="$activeDirection">Completed</x-table.head>
                <x-table.head sortable="updated_at" :sort="$activeSort" :direction="$activeDirection">Updated</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        @forelse ($rows as $project)
            <x-table.expandable-row colspan="8" :label="'Show description of '.$project->name" :detail="$project->description" wire:key="project-{{ $project->uuid }}">
                <x-table.cell label="Name">
                    <a href="{{ route('projects.show', $project) }}" wire:navigate>{{ $project->name }}</a>
                </x-table.cell>
                <x-table.cell label="Client">{{ $project->client->name }}</x-table.cell>
                <x-table.cell label="Start" class="table__cell--nowrap">{{ DateFormatter::format($project->start_date, 'Not started') }}</x-table.cell>
                <x-table.cell label="Due" class="table__cell--nowrap">
                    @if ($project->isOverdue())
                        <x-badge variant="error">{{ DateFormatter::format($project->due_date) }}</x-badge>
                    @else
                        {{ DateFormatter::format($project->due_date, 'Not due') }}
                    @endif
                </x-table.cell>
                <x-table.cell label="Completed" class="table__cell--nowrap">{{ DateFormatter::format($project->completed_date, 'Incomplete') }}</x-table.cell>
                <x-table.cell label="Updated" class="table__cell--nowrap">{{ $project->updated_at->diffForHumans() }}</x-table.cell>
                <x-table.cell>
                    <div class="table__actions">
                        <x-button variant="ghost" size="sm" icon="eye" :label="'View '.$project->name" :href="route('projects.show', $project)" wire:navigate />
                    </div>
                </x-table.cell>
            </x-table.expandable-row>
        @empty
            <tbody>
                <x-table.empty colspan="8">No projects found.</x-table.empty>
            </tbody>
        @endforelse
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="projects" />

    <x-dialog name="project-form" title="Add project">
        @if ($clientOptions->isEmpty())
            <p>You need an active client first. <a href="{{ route('clients.index') }}" wire:navigate>Add a client</a>.</p>
        @else
            @include('livewire.projects.form-fields')
        @endif
    </x-dialog>
</div>
