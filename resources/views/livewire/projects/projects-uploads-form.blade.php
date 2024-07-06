<x-modals.base
    id="projects_uploads_form__modal"
    title="Project Uploads"
    :subtitle="$project_name ? 'For ' . $project_name : null"
>
    <form wire:submit.prevent="uploadFiles">
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
