<x-layouts.page title="Client Details">
    <x-slot:action-button>
        <x-modals.trigger
            id="clients_form__modal"
            label="Edit Details"
            icon="edit"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('clients.clients-form', 'edit', { id: {{ $client->id }}})"
        />
    </x-slot:action-button>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <x-layouts.card>
                <div class="flex flex-col gap-4">
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

    <hr class="dark:border-gray-600 my-4">

    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">Contacts</h2>

        <x-modals.trigger
            id="clients_contacts_form__modal"
            label="Add Contacts"
            icon="plus"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('clients.clients-contacts-form', 'getClient', { id: {{ $client->id }}})"
        />
    </div>

    <livewire:clients.clients-contacts-table :client-id="$client->id" />

    <hr class="dark:border-gray-600 my-4">

    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-3xl">Uploads</h2>

        <x-modals.trigger
            id="clients_uploads_form__modal"
            label="Upload Files"
            icon="file"
            class="btn-primary btn-sm"
            wire:click="$dispatchTo('clients.clients-uploads-form', 'getClient', { id: {{ $client->id }}})"
        />
    </div>

    <livewire:clients.clients-uploads-table :client-id="$client->id" />

    {{-- Modal Forms --}}
    <livewire:clients.clients-form />
    <livewire:clients.clients-contacts-form />
    <livewire:clients.clients-uploads-form />
</x-layouts.page>
