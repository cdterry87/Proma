<div class="page">
    <x-page-header :title="$client->name">
        <x-slot:actions>
            <x-button icon="edit" dialog="client-form">Edit details</x-button>
            <x-button variant="danger" icon="delete" dialog="client-delete">Delete client</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div>
            <x-badge :variant="$client->active ? 'success' : null">{{ $client->active ? 'Active' : 'Inactive' }}</x-badge>
        </div>
        <x-show-more :text="$client->description" />
        <x-timestamps :created="$client->created_at" :updated="$client->updated_at" />
    </x-card>

    <div class="grid-auto">
        <x-stat label="Incomplete projects" :value="$incompleteProjectCount" :hint="$completedProjectCount.' complete'" icon="projects" />
        <x-stat label="Unresolved issues" :value="$unresolvedIssueCount" :hint="$resolvedIssueCount.' resolved'" icon="issues" />
    </div>

    <div class="split">
        <x-card title="Incomplete projects" :subtitle="$incompleteProjectCount > $relatedLimit ? 'Nearest '.$relatedLimit.' by due date' : null">
            @if ($projects->isEmpty())
                <p class="muted">No incomplete projects.</p>
            @else
                <ul class="related-list" role="list">
                    @foreach ($projects as $project)
                        <li wire:key="project-{{ $project->id }}">
                            <a href="{{ url('/projects/'.$project->id) }}" wire:navigate>{{ $project->name }}</a>
                            <span class="muted">{{ $project->due_date ? 'Due '.\App\Support\DateFormatter::format($project->due_date) : 'No due date' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card title="Unresolved issues" :subtitle="$unresolvedIssueCount > $relatedLimit ? 'Highest '.$relatedLimit.' by priority' : null">
            @if ($issues->isEmpty())
                <p class="muted">No unresolved issues.</p>
            @else
                <ul class="related-list" role="list">
                    @foreach ($issues as $issue)
                        <li wire:key="issue-{{ $issue->id }}">
                            <a href="{{ url('/issues/'.$issue->id) }}" wire:navigate>{{ $issue->name }}</a>
                            <x-badge :variant="$issue->priority->badgeVariant()">{{ $issue->priority->label() }}</x-badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>

    <livewire:contacts.manager :client="$client" />

    <x-dialog name="client-form" title="Edit client">
        @include('livewire.clients.form-fields')
    </x-dialog>

    <x-confirm-dialog name="client-delete" title="Delete client" action="delete">
        This permanently deletes {{ $client->name }}, along with its contacts ({{ $contactCount }}), projects ({{ $incompleteProjectCount + $completedProjectCount }}), issues ({{ $unresolvedIssueCount + $resolvedIssueCount }}), and all of their tasks.
    </x-confirm-dialog>
</div>
