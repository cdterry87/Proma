@use('App\Support\DateFormatter')

<div class="page">
    <x-page-header :title="$project->name">
        <x-slot:actions>
            <x-button icon="edit" dialog="project-form">Edit details</x-button>
            <x-button variant="danger" icon="delete" dialog="project-delete">Delete project</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="cluster">
            <x-badge :variant="$project->isCompleted() ? 'success' : null">{{ $project->isCompleted() ? 'Completed' : 'Incomplete' }}</x-badge>
            @if ($project->isOverdue())
                <x-badge variant="error">Past due</x-badge>
            @endif
        </div>
        <x-show-more :text="$project->description" />
        <dl class="details">
            <div>
                <dt>Client</dt>
                <dd><a href="{{ route('clients.show', $client) }}" wire:navigate>{{ $client->name }}</a></dd>
            </div>
            <div>
                <dt>Start date</dt>
                <dd>{{ DateFormatter::format($project->start_date, 'Not started') }}</dd>
            </div>
            <div>
                <dt>Due date</dt>
                <dd>{{ DateFormatter::format($project->due_date, 'Not due') }}</dd>
            </div>
            <div>
                <dt>Completed</dt>
                <dd>{{ DateFormatter::format($project->completed_date, 'Incomplete') }}</dd>
            </div>
        </dl>
        <x-timestamps :created="$project->created_at" :updated="$project->updated_at" />
    </x-card>

    <div class="grid-auto">
        <x-stat label="Unresolved issues" :value="$issues->count()" :hint="$resolvedIssueCount.' resolved'" icon="issues" />
        <x-stat label="Incomplete tasks" :value="$incompleteTaskCount" :hint="$completedTaskCount.' complete'" icon="tasks" />
    </div>

    <x-card title="Unresolved issues">
        @if ($issues->isEmpty())
            <p class="muted">No unresolved issues.</p>
        @else
            <ul class="related-list" role="list">
                @foreach ($issues as $issue)
                    <li wire:key="issue-{{ $issue->id }}">
                        <a href="{{ route('issues.show', $issue) }}" wire:navigate>{{ $issue->name }}</a>
                        <x-badge :variant="$issue->priority->badgeVariant()">{{ $issue->priority->label() }}</x-badge>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-card>

    <livewire:tasks.manager :taskable="$project" />

    <x-dialog name="project-form" title="Edit project">
        @include('livewire.projects.form-fields')
    </x-dialog>

    <x-confirm-dialog name="project-delete" title="Delete project" action="delete">
        This permanently deletes {{ $project->name }}, along with its tasks ({{ $incompleteTaskCount + $completedTaskCount }}), issues ({{ $issues->count() + $resolvedIssueCount }}), and all of their tasks.
    </x-confirm-dialog>
</div>
