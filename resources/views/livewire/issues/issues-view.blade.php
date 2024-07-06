<x-layouts.page title="Issue Details">
    <x-slot:action-button>
        <x-modals.trigger
            id="issues_form__modal"
            label="Edit Details"
            icon="edit"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('issues.issues-form', 'edit', { id: {{ $issue->id }}})"
        />
    </x-slot:action-button>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <x-layouts.card>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <x-elements.badge
                            label="{{ $issue->resolved_date ? 'Resolved ' . $issue->resolved_date : 'Unresolved' }}"
                            class="{{ $issue->resolved_date ? 'badge-success' : 'badge-error' }}"
                        />
                        <x-elements.badge
                            label="{{ $priorityCodes->firstWhere('value', $issue->priority)['label'] }}"
                            class="{{ $priorityCodes->firstWhere('value', $issue->priority)['class'] }}"
                        />
                    </div>

                    <x-inputs.display
                        label="Name"
                        value="{{ $issue->name }}"
                    />
                    <x-inputs.display
                        label="Description"
                        value="{{ $issue->description }}"
                    />
                    <x-inputs.display
                        label="Client"
                        value="{{ $issue->client->name ?? 'N/A' }}"
                    />
                    <x-inputs.display
                        label="Project"
                        value="{{ $issue->project->name ?? 'N/A' }}"
                    />

                    <x-elements.timestamps
                        :created-at="$issue->created_at"
                        :updated-at="$issue->updated_at"
                    />
                </div>
            </x-layouts.card>
        </div>
        <div class="lg:col-span-2">
            <x-layouts.card title="Issue Stats">
                <div class="stats stats-vertical shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <x-icons.tasks />
                        </div>
                        <div class="stat-title">Incomplete Tasks</div>
                        <div class="stat-value text-primary">{{ $incompleteTasksCount }}</div>
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
            id="issues_tasks_form__modal"
            label="Add Tasks"
            icon="plus"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('issues.issues-tasks-form', 'getIssue', { id: {{ $issue->id }}})"
        />
    </div>

    <livewire:issues.issues-tasks-table :issue-id="$issue->id" />

    <hr class="dark:border-gray-600 my-4">

    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">Uploads</h2>

        @if (auth()->user()->demo)
            <p class="font-semibold text-xs text-error">Uploads are disabled for demo users.</p>
        @else
            <x-modals.trigger
                id="issues_uploads_form__modal"
                label="Upload Files"
                icon="file"
                class="btn-primary btn-sm"
                wire:click="$dispatchTo('issues.issues-uploads-form', 'getIssue', { id: {{ $issue->id }}})"
            />
        @endif
    </div>

    <livewire:issues.issues-uploads-table :issue-id="$issue->id" />

    {{-- Delete Confirmation --}}
    <div class="w-full flex items-center justify-center mt-8">
        <x-modals.delete-confirmation
            id="delete_issue__modal"
            label="Delete Issue"
            action="deleteIssue"
        />
    </div>

    {{-- Modal Forms --}}
    <livewire:issues.issues-form />
    <livewire:issues.issues-tasks-form />
    <livewire:issues.issues-uploads-form />
</x-layouts.page>
