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
                :class="{ 'tab-active': activeTab === 'contacts' }"
                @click.prevent="activeTab = 'contacts'"
            >Contacts</a>
            <a
                role="tab"
                class="tab"
                :class="{ 'tab-active': activeTab === 'notes' }"
                @click.prevent="activeTab = 'notes'"
            >Notes</a>
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
                    <x-layouts.card title="Client Details">
                        <x-slot:top-actions>
                            <x-modals.trigger
                                id="clients_form__modal"
                                label="Edit"
                                icon="edit"
                                class="btn-primary btn-sm"
                                wire:click="$dispatchTo('clients.clients-form', 'edit', { id: {{ $client->id }}})"
                            />
                        </x-slot:top-actions>

                        <div class="flex flex-col gap-4">
                            <x-alerts.container />

                            <x-elements.badge
                                label="{{ $client->active ? 'Active' : 'Inactive' }}"
                                class="{{ $client->active ? 'badge-success' : 'badge-error' }}"
                            />
                            <x-inputs.display
                                label="Name"
                                value="{{ $client->name }}"
                            />
                            <x-inputs.display
                                label="Description"
                                value="{{ $client->description }}"
                            />
                        </div>
                    </x-layouts.card>
                </div>
                <div class="lg:col-span-2">
                    <x-layouts.card title="Client Stats">
                        <div class="stats stats-vertical shadow">
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

        {{-- Contacts --}}
        <div
            x-show="activeTab === 'contacts'"
            role="tabpanel"
        >
            <x-layouts.card title="Client Contacts">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="clients_contacts__modal"
                        label="Add Contacts"
                        icon="plus"
                        class="btn-secondary btn-sm"
                        wire:click="$dispatchTo('clients.clients-contacts', 'getClient', { id: {{ $client->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:clients.clients-contacts-table :client-id="$client->id" />
            </x-layouts.card>
        </div>

        {{-- Notes --}}
        <div
            x-show="activeTab === 'notes'"
            role="tabpanel"
        >
            <x-layouts.card title="Client Notes">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="clients_notes__modal"
                        label="Add Note"
                        icon="plus"
                        class="btn-secondary btn-sm"
                        wire:click="$dispatchTo('clients.clients-notes', 'getClient', { id: {{ $client->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:clients.clients-notes-table :client-id="$client->id" />
            </x-layouts.card>
        </div>

        {{-- Uploads --}}
        <div
            x-show="activeTab === 'uploads'"
            role="tabpanel"
        >
            <x-layouts.card title="Client Uploads">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="clients_uploads__modal"
                        label="Upload Files"
                        icon="plus"
                        class="btn-secondary btn-sm"
                        wire:click="$dispatchTo('clients.clients-uploads', 'getClient', { id: {{ $client->id }}})"
                    />
                </x-slot:top-actions>

                <livewire:clients.clients-uploads-table :client-id="$client->id" />
            </x-layouts.card>
        </div>
    </div>

    {{-- Components --}}
    <livewire:clients.clients-form />
    <livewire:clients.clients-contacts />
    <livewire:clients.clients-notes />
    <livewire:clients.clients-uploads />
</div>
