<x-modals.base
    id="teams_form__modal"
    title="Teams Form"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
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
                required
            />
            <x-inputs.textarea
                label="Description"
                name="description"
                placeholder="Description"
                wire:model="description"
                required
            />
            <div class="mt-4">
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save"
                />
            </div>
        </div>
    </form>
</x-modals.base>
