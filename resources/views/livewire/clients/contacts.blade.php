<x-modals.base
    id="clients_contacts__modal"
    title="Client Contacts"
    :subtitle="$client_name ? 'For ' . $client_name : null"
>
    <form wire:submit.prevent="addContact">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <div class="flex justify-end">
                    <x-inputs.toggle
                        label="Active"
                        name="active"
                        id="active"
                        wire:model="active"
                    />
                </div>
                <x-inputs.text
                    label="Name"
                    name="name"
                    placeholder="Name"
                    wire:model="name"
                    maxlength="255"
                    required
                />
                <x-inputs.text
                    label="Title"
                    name="title"
                    placeholder="Title"
                    wire:model="title"
                    maxlength="255"
                    required
                />
                <x-inputs.text
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="Email"
                    wire:model="email"
                    maxlength="255"
                />
                <div class="flex gap-2">
                    <div class="flex-1">
                        <x-inputs.text
                            label="Phone"
                            name="phone"
                            placeholder="Phone"
                            wire:model="phone"
                            maxlength="30"
                        />
                    </div>
                    <div>
                        <x-inputs.text
                            label="Ext."
                            name="phone_ext"
                            placeholder="Phone Ext."
                            wire:model="phone_ext"
                            maxlength="5"
                        />
                    </div>
                </div>
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Add Contact"
                />
            </div>

            <hr>

            <div class="flex flex-col gap-2 w-full">
                @if ($clientContacts->isNotEmpty())
                    @foreach ($clientContacts as $clientContact)
                        <div class="stats shadow bg-gray-200 dark:bg-gray-800">
                            <div class="stat">
                                <div class="stat-figure text-error">
                                    <button
                                        class="btn btn-ghost btn-circle"
                                        wire:click="deleteContact({{ $clientContact->id }})"
                                    >
                                        <x-icons.delete />
                                    </button>
                                </div>
                                <div class="text-lg text-primary">{{ $clientContact->name }}</div>
                                <div class="stat-desc">{{ $clientContact->email }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">No contacts for this client.</p>
                @endif
            </div>
        </div>
    </form>
</x-modals.base>
