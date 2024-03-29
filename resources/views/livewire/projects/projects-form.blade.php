<x-modals.base
    id="projects_form__modal"
    title="Projects Form"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
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
            <x-inputs.select
                label="Client"
                name="client_id"
                wire:model="client_id"
                :options="$clients"
                default-option="Select Client"
            />
            <x-inputs.select
                label="Team"
                name="team_id"
                wire:model="team_id"
                :options="$teams"
                default-option="Select Team"
            />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-inputs.text
                    label="Start Date"
                    name="start_date"
                    wire:model="start_date"
                    type="date"
                />
                <x-inputs.text
                    label="Due Date"
                    name="due_date"
                    wire:model="due_date"
                    type="date"
                />
            </div>
            <div class="mt-4">
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save"
                />
            </div>
        </div>
    </form>
</x-modals.base>
