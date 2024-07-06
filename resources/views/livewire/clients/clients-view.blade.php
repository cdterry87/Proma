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

                    <x-elements.timestamps
                        :created-at="$client->created_at"
                        :updated-at="$client->updated_at"
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
                        <div class="stat-title">Incomplete Projects</div>
                        <div class="stat-value text-primary">{{ $incompleteProjectsCount }}</div>
                        <div class="stat-desc text-accent">{{ $completeProjectsCount }} Projects Complete</div>
                    </div>

                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <x-icons.issues />
                        </div>
                        <div class="stat-title">Unresolved Issues</div>
                        <div class="stat-value text-secondary">{{ $unresolvedIssuesCount }}</div>
                        <div class="stat-desc text-accent">{{ $resolvedIssuesCount }} Issues Resolved</div>
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

        @if (auth()->user()->guest)
            <p class="font-semibold text-xs text-error">Uploads are disabled for guests.</p>
        @else
            <x-modals.trigger
                id="clients_uploads_form__modal"
                label="Upload Files"
                icon="file"
                class="btn-primary btn-sm"
                wire:click="$dispatchTo('clients.clients-uploads-form', 'getClient', { id: {{ $client->id }}})"
            />
        @endif
    </div>

    <livewire:clients.clients-uploads-table :client-id="$client->id" />

    {{-- Delete Confirmation --}}
    <div class="w-full flex items-center justify-center mt-8">
        <x-modals.delete-confirmation
            id="delete_client__modal"
            label="Delete Client"
            action="deleteClient"
        />
    </div>

    {{-- Modal Forms --}}
    <livewire:clients.clients-form />
    <livewire:clients.clients-contacts-form />
    <livewire:clients.clients-uploads-form />
</x-layouts.page>
