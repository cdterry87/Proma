<x-modals.base
    id="clients_contacts__modal"
    title="Client Contacts"
    :subtitle="$client_name ? 'For ' . $client_name : null"
>
    <form wire:submit.prevent="saveContact">
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
                    required
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
                    label="Save Contact"
                />
            </div>
        </div>
    </form>
</x-modals.base>
