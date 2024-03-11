<div>
    <div class="flex flex-col gap-8">
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

        <x-layouts.card title="Client Contacts">
            <x-slot:top-actions>
                <x-modals.trigger
                    id="clients_contacts__modal"
                    label="Add Contacts"
                    icon="plus"
                    class="btn-secondary btn-sm"
                    wire:click="$dispatchTo('clients.clients-contacts', 'addContacts', { id: {{ $client->id }}})"
                />
            </x-slot:top-actions>

            <livewire:clients.clients-contacts-table :client-id="$client->id" />
        </x-layouts.card>
    </div>

    <livewire:clients.clients-form />
    <livewire:clients.clients-contacts />
</div>
