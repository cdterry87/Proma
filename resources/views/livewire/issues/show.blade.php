@use('App\Support\DateFormatter')

<div class="page">
    <x-page-header :title="$issue->name">
        <x-slot:actions>
            <x-button icon="edit" dialog="issue-form">Edit details</x-button>
            <x-button variant="danger" icon="delete" dialog="issue-delete">Delete issue</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="cluster">
            <x-badge :variant="$issue->isResolved() ? 'success' : null">{{ $issue->isResolved() ? 'Resolved' : 'Unresolved' }}</x-badge>
            <x-badge :variant="$issue->priority->badgeVariant()">{{ $issue->priority->label() }}</x-badge>
        </div>
        <x-show-more :text="$issue->description" />
        <dl class="details">
            <div>
                <dt>Client</dt>
                <dd><a href="{{ route('clients.show', $client) }}" wire:navigate>{{ $client->name }}</a></dd>
            </div>
            <div>
                <dt>Project</dt>
                <dd>
                    @if ($project)
                        <a href="{{ route('projects.show', $project) }}" wire:navigate>{{ $project->name }}</a>
                    @else
                        None
                    @endif
                </dd>
            </div>
            <div>
                <dt>Resolved</dt>
                <dd>{{ DateFormatter::format($issue->resolved_date, 'Unresolved') }}</dd>
            </div>
        </dl>
        <x-timestamps :created="$issue->created_at" :updated="$issue->updated_at" />
    </x-card>

    <div class="grid-auto">
        <x-stat label="Incomplete tasks" :value="$incompleteTaskCount" :hint="$completedTaskCount.' complete'" icon="tasks" />
    </div>

    <livewire:tasks.manager :taskable="$issue" />

    <x-dialog name="issue-form" title="Edit issue">
        @include('livewire.issues.form-fields')
    </x-dialog>

    <x-confirm-dialog name="issue-delete" title="Delete issue" action="delete">
        This permanently deletes {{ $issue->name }}, along with its tasks ({{ $incompleteTaskCount + $completedTaskCount }}).
    </x-confirm-dialog>
</div>
