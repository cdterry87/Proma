<x-modals.base
    id="projects_tasks_form__modal"
    title="Project Tasks"
    :subtitle="$project_name ? 'For ' . $project_name : null"
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
                <div class="flex items-center gap-4">
                    <div class="w-full">
                        <x-inputs.button
                            class="btn-primary btn-block"
                            label="Save Task"
                        />
                    </div>
                    @if ($task_id)
                        <div class="w-full">
                            @if ($completed_date)
                                <x-inputs.button
                                    class="btn-error btn-block"
                                    label="Incomplete"
                                    icon="error"
                                    wire:click.prevent="toggleCompleteTask"
                                />
                            @else
                                <x-inputs.button
                                    class="btn-success btn-block"
                                    label="Complete"
                                    icon="success"
                                    wire:click.prevent="toggleCompleteTask"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</x-modals.base>
