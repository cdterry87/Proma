<x-layouts.page title="Project Details">
    <x-slot:action-button>
        <x-modals.trigger
            id="projects_form__modal"
            label="Edit Details"
            icon="edit"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('projects.projects-form', 'edit', { id: {{ $project->id }}})"
        />
    </x-slot:action-button>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <x-layouts.card>
                <div class="flex flex-col gap-4">
                    <x-elements.badge
                        label="{{ $project->completed_date ? 'Completed ' . Carbon\Carbon::parse($project->completed_date)->format('m/d/Y') : 'Incomplete' }}"
                        class="{{ $project->completed_date ? 'badge-success' : 'badge-error' }}"
                    />
                    <x-inputs.display
                        label="Name"
                        value="{{ $project->name }}"
                    />
                    <x-inputs.display
                        label="Description"
                        value="{{ $project->description }}"
                    />
                    <x-inputs.display
                        label="Client"
                        value="{{ $project->client->name ?? 'N/A' }}"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-inputs.display
                            label="Start Date"
                            value="{{ Carbon\Carbon::parse($project->start_date)->format('m/d/Y') ?? 'N/A' }}"
                        />
                        <x-inputs.display
                            label="Due Date"
                            value="{{ Carbon\Carbon::parse($project->due_date)->format('m/d/Y') ?? 'N/A' }}"
                        />
                    </div>
                    <x-elements.timestamps
                        :created-at="$project->created_at"
                        :updated-at="$project->updated_at"
                    />
                </div>
            </x-layouts.card>
        </div>
        <div class="lg:col-span-2">
            <x-layouts.card title="Project Stats">
                <div class="stats stats-vertical shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <x-icons.issues />
                        </div>
                        <div class="stat-title">Unresolved Issues</div>
                        <div class="stat-value text-primary">{{ $unresolvedIssuesCount }}</div>
                        <div class="stat-desc text-accent">{{ $resolvedIssuesCount }} Issues Resolved</div>
                    </div>
                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <x-icons.tasks />
                        </div>
                        <div class="stat-title">Incomplete Tasks</div>
                        <div class="stat-value text-secondary">{{ $incompleteTasksCount }}</div>
                        <div class="stat-desc text-accent">{{ $completeTasksCount }} Tasks Complete</div>
                    </div>
                </div>
            </x-layouts.card>
        </div>
    </div>

    <hr class="dark:border-gray-600 my-4">

    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">Tasks</h2>

        <x-modals.trigger
            id="projects_tasks_form__modal"
            label="Add Tasks"
            icon="plus"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('projects.projects-tasks-form', 'getProject', { id: {{ $project->id }}})"
        />
    </div>

    <livewire:projects.projects-tasks-table :project-id="$project->id" />

    <hr class="dark:border-gray-600 my-4">

    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">Uploads</h2>

        @if (auth()->user()->guest)
            <p class="font-semibold text-xs text-error">Uploads are disabled for guests.</p>
        @else
            <x-modals.trigger
                id="projects_uploads_form__modal"
                label="Upload Files"
                icon="file"
                class="btn-primary btn-sm"
                wire:click="$dispatchTo('projects.projects-uploads-form', 'getProject', { id: {{ $project->id }}})"
            />
        @endif
    </div>

    <livewire:projects.projects-uploads-table :project-id="$project->id" />

    {{-- Delete Confirmation --}}
    <div class="w-full flex items-center justify-center mt-8">
        <x-modals.delete-confirmation
            id="delete_project__modal"
            label="Delete Project"
            action="deleteProject"
        />
    </div>

    {{-- Modal Forms --}}
    <livewire:projects.projects-form />
    <livewire:projects.projects-tasks-form />
    <livewire:projects.projects-uploads-form />
</x-layouts.page>
