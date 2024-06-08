<x-modals.base
    id="issues_tasks_form__modal"
    title="Issue Tasks"
    :subtitle="$issue_name ? 'For ' . $issue_name : null"
>
    <form wire:submit.prevent="saveTask">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <x-inputs.text
                    label="Title"
                    name="title"
                    placeholder="Title"
                    wire:model="title"
                    required
                />
                <x-inputs.textarea
                    label="Description"
                    name="description"
                    wire:model="description"
                    required
                />
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save Task"
                />
            </div>
        </div>
    </form>
</x-modals.base>
