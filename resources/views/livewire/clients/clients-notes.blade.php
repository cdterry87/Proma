<x-modals.base
    id="clients_notes__modal"
    title="Client Notes"
    :subtitle="$client_name ? 'For ' . $client_name : null"
>
    <form wire:submit.prevent="saveNote">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <x-inputs.textarea
                    label="Note"
                    name="note"
                    wire:model="note"
                    required
                />
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save Note"
                />
            </div>
        </div>
    </form>
</x-modals.base>
