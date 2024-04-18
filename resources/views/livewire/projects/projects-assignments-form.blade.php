<x-modals.base
    id="projects_assignments_form__modal"
    title="Assignments"
    :subtitle="$project_name ? 'For ' . $project_name : null"
>
    <form wire:submit.prevent="saveAssignment">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <x-inputs.select
                    label="Assigned To"
                    name="assigned_to"
                    wire:model="assigned_to"
                >
                    <option value="">Select User</option>
                    @foreach ($assignableUsers as $assignableUser)
                        <option value="{{ $assignableUser->id }}">{{ $assignableUser->name }}</option>
                    @endforeach
                </x-inputs.select>
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Assign"
                />
            </div>
        </div>
    </form>
</x-modals.base>
