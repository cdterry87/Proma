<x-modals.base
    id="clients_uploads__modal"
    title="Client Uploads"
    :subtitle="$client_name ? 'For ' . $client_name : null"
>
    <form wire:submit.prevent="uploadFile">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <x-inputs.file
                    wire:model="files"
                    label="Upload"
                    name="files"
                    id="upload_{{ $uploadIteration }}"
                    multiple
                />
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Upload Files"
                />
            </div>
        </div>
    </form>
</x-modals.base>
