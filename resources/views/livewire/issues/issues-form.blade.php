<x-modals.base
    id="issues_form__modal"
    title="Issues Form"
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
                label="Priority"
                name="priority"
                wire:model="priority"
                required
            >
                <option value="">Select Priority</option>
                @foreach ($priorityCodes as $priority)
                    <option value="{{ $priority['value'] }}">{{ $priority['label'] }}</option>
                @endforeach
            </x-inputs.select>
            <x-inputs.select
                label="Client"
                name="client_id"
                wire:model.live="client_id"
                required
            >
                <option value="">Select Client</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </x-inputs.select>
            @if ($client_id)
                <x-inputs.select
                    label="Project"
                    name="project_id"
                    wire:model="project_id"
                    required
                >
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </x-inputs.select>
            @endif
            <div class="mt-4 flex items-center gap-4">
                <div class="w-full">
                    <x-inputs.button
                        class="btn-primary btn-block"
                        label="Save"
                    />
                </div>
                @if ($model_id)
                    <div class="w-full">
                        @if ($resolved_date)
                            <x-inputs.button
                                class="btn-error btn-block"
                                label="Unresolve"
                                icon="error"
                                wire:click.prevent="toggleResolveIssue"
                            />
                        @else
                            <x-inputs.button
                                class="btn-success btn-block"
                                label="Resolve"
                                icon="success"
                                wire:click.prevent="toggleResolveIssue"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </form>
</x-modals.base>
