<div x-data="{ activeTab: 'details' }">
    <div class="flex flex-col gap-8">
        {{-- Tabs --}}
        <div
            role="tablist"
            class="tabs tabs-bordered"
        >
            <a
                role="tab"
                class="tab"
                :class="{ 'tab-active': activeTab === 'details' }"
                @click.prevent="activeTab = 'details'"
            >Details</a>
            <a
                role="tab"
                class="tab"
                :class="{ 'tab-active': activeTab === 'tasks' }"
                @click.prevent="activeTab = 'tasks'"
            >Tasks</a>
            <a
                role="tab"
                class="tab"
                :class="{ 'tab-active': activeTab === 'uploads' }"
                @click.prevent="activeTab = 'uploads'"
            >Uploads</a>
        </div>

        {{-- Details --}}
        <div
            x-show="activeTab === 'details'"
            role="tabpanel"
        >
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3">
                    <x-layouts.card title="Project Details">
                        <x-slot:top-actions>
                            <x-modals.trigger
                                id="projects_form__modal"
                                icon="edit"
                                label="Edit"
                                class="btn-primary btn-sm"
                                wire:click="$dispatchTo('projects.projects-form', 'edit', { id: {{ $project->id }}})"
                            />
                        </x-slot:top-actions>

                        <div class="flex flex-col gap-4">
                            <x-elements.badge
                                label="{{ $project->completed_date ? 'Completed ' . $project->completed_date : 'Incomplete' }}"
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-inputs.display
                                    label="Client"
                                    value="{{ $project->client->name ?? 'N/A' }}"
                                />
                                <x-inputs.display
                                    label="Team"
                                    value="{{ $project->team->name ?? 'N/A' }}"
                                />
                                <x-inputs.display
                                    label="Start Date"
                                    value="{{ $project->start_date }}"
                                />
                                <x-inputs.display
                                    label="Due Date"
                                    value="{{ $project->due_date }}"
                                />
                            </div>
                        </div>
                    </x-layouts.card>
                </div>
                <div class="lg:col-span-2">
                    <livewire:projects.projects-assignments :project-id="$project->id" />
                </div>
            </div>
        </div>

        {{-- Tasks --}}
        <div
            x-show="activeTab === 'tasks'"
            role="tabpanel"
        >
            <x-layouts.card title="Project Tasks">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="projects_tasks__modal"
                        label="Add Task"
                        icon="plus"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('projects.projects-tasks', 'getProject', { id: {{ $project->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:projects.projects-tasks-table :project-id="$project->id" />
            </x-layouts.card>
        </div>

        {{-- Uploads --}}
        <div
            x-show="activeTab === 'uploads'"
            role="tabpanel"
        >
            <x-layouts.card title="Project Uploads">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="projects_uploads__modal"
                        label="Upload Files"
                        icon="plus"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('projects.projects-uploads', 'getProject', { id: {{ $project->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:projects.projects-uploads-table :project-id="$project->id" />
            </x-layouts.card>
        </div>
    </div>

    {{-- Components --}}
    <livewire:projects.projects-form />
    <livewire:projects.projects-assignments-form />
    <livewire:projects.projects-tasks />
    <livewire:projects.projects-uploads />
</div>
