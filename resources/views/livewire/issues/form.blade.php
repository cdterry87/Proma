<x-modals.base
    id="issues_form__modal"
    title="Issues Form"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
            <x-inputs.textarea
                label="Description"
                name="description"
                placeholder="Description"
                wire:model="description"
                required
            />
            <x-inputs.select
                label="Priority"
                name="priority"
                wire:model="priority"
                required
            >
                <option value="">Select Priority</option>
                <option value="1">Lowest</option>
                <option value="2">Low</option>
                <option value="3">Medium</option>
                <option value="4">High</option>
                <option value="5">Highest</option>
            </x-inputs.select>
            <x-inputs.select
                label="Client"
                name="client_id"
                wire:model="client_id"
            >
                <option value="">Select Client</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </x-inputs.select>
            <x-inputs.select
                label="Team"
                name="team_id"
                wire:model="team_id"
            >
                <option value="">Select Team</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </x-inputs.select>
            <x-inputs.select
                label="Project"
                name="project_id"
                wire:model="project_id"
            >
                <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </x-inputs.select>
            <x-inputs.select
                label="Assigned To"
                name="assigned_to"
                wire:model="assigned_to"
            >
                <option value="">Select Assigned To</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-inputs.select>
            <div class="mt-4">
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save"
                />
            </div>
        </div>
    </form>
</x-modals.base>
