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
                :class="{ 'tab-active': activeTab === 'members' }"
                @click.prevent="activeTab = 'members'"
            >Members</a>
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
                <div class="lg:col-span-3 flex flex-col gap-8">
                    <x-layouts.card title="Team Details">
                        <x-slot:top-actions>
                            <x-modals.trigger
                                id="teams_form__modal"
                                label="Edit"
                                icon="edit"
                                class="btn-primary btn-sm"
                                wire:click="$dispatchTo('teams.teams-form', 'edit', { id: {{ $team->id }}})"
                            />
                        </x-slot:top-actions>

                        <div class="flex flex-col gap-4">
                            <x-alerts.container />

                            <x-elements.badge
                                label="{{ $team->active ? 'Active' : 'Inactive' }}"
                                class="{{ $team->active ? 'badge-success' : 'badge-error' }}"
                            />
                            <x-inputs.display
                                label="Name"
                                value="{{ $team->name }}"
                            />
                            <x-inputs.display
                                label="Description"
                                value="{{ $team->description }}"
                            />
                        </div>
                    </x-layouts.card>
                </div>
                <div class="lg:col-span-2">
                    <x-layouts.card title="Team Stats">
                        <div class="stats stats-vertical shadow">
                            <div class="stat">
                                <div class="stat-figure">
                                    <x-icons.users />
                                </div>
                                <div class="stat-title">Active Members</div>
                                <div class="stat-value">{{ $teamUsersActiveCount }}</div>
                                <div class="stat-desc text-accent">{{ $teamUsersInactiveCount }} Members Inactive</div>
                            </div>

                            <div class="stat">
                                <div class="stat-figure text-primary">
                                    <x-icons.projects />
                                </div>
                                <div class="stat-title">Active Projects</div>
                                <div class="stat-value text-primary">0</div>
                                <div class="stat-desc text-accent">0 Projects Complete</div>
                            </div>

                            <div class="stat">
                                <div class="stat-figure text-secondary">
                                    <x-icons.issues />
                                </div>
                                <div class="stat-title">Active Issues</div>
                                <div class="stat-value text-secondary">0</div>
                                <div class="stat-desc text-accent">0 Issues Closed</div>
                            </div>

                        </div>
                    </x-layouts.card>
                </div>
            </div>
        </div>

        {{-- Members --}}
        <div
            x-show="activeTab === 'members'"
            role="tabpanel"
        >
            <x-layouts.card title="Team Members">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="teams_users__modal"
                        label="Add Members"
                        icon="plus"
                        class="btn-secondary btn-sm"
                        wire:click="$dispatchTo('teams.teams-users', 'addMembers', { id: {{ $team->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:teams.teams-users-table :team-id="$team->id" />
            </x-layouts.card>
        </div>
    </div>

    {{-- Components --}}
    <livewire:teams.teams-form />
    <livewire:teams.teams-users />
</div>
