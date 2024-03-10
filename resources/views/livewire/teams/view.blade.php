<div>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <x-layouts.card title="Team Details">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="teams_form__modal"
                        label="Edit"
                        icon="edit"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('teams.form', 'edit', { id: {{ $team->id }}})"
                    />
                </x-slot:top-actions>

                <div class="flex flex-col gap-4">
                    <x-alerts.container />

                    <x-inputs.display
                        label="Active"
                        value="{{ $team->active ? 'Yes' : 'No' }}"
                    />
                    <x-inputs.display
                        label="Name"
                        value="{{ $team->name }}"
                    />
                    <x-inputs.display
                        label="Description"
                        value="{{ $team->description }}"
                    />
                </div>
            </x-layouts.card>
        </div>
        <div class="lg:col-span-2">
            <x-layouts.card title="Team Members">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="teams_users__modal"
                        label="Edit"
                        icon="edit"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('teams.users', 'edit', { id: {{ $team->id }}})"
                    />
                </x-slot:top-actions>

                @if ($teamUsers->isNotEmpty())
                    <ul class="list-inside list-disc">
                        @foreach ($teamUsers as $teamUser)
                            <li>{{ $teamUser->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No users on this team.</p>
                @endif
            </x-layouts.card>
        </div>
    </div>

    <livewire:teams.form />
    <livewire:teams.users />
</div>
