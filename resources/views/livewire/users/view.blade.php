<div>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <x-layouts.card title="User Details">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="users_form__modal"
                        label="Edit"
                        icon="edit"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('users.form', 'edit', { id: {{ $user->id }}})"
                    />
                </x-slot:top-actions>

                <div class="flex flex-col gap-4">
                    <x-alerts.container />

                    <x-inputs.display
                        label="Active"
                        value="{{ $user->active ? 'Yes' : 'No' }}"
                    />
                    <x-inputs.display
                        label="Name"
                        value="{{ $user->name }}"
                    />
                    <x-inputs.display
                        label="Email"
                        value="{{ $user->email }}"
                    />
                    <x-inputs.display
                        label="Title"
                        value="{{ $user->title }}"
                    />
                </div>
                <x-slot:bottom-actions>
                    <x-inputs.button
                        label="Reset Password"
                        icon="lock"
                        class="btn btn-secondary btn-sm"
                        wire:click.prevent="resetPassword"
                    />
                </x-slot:bottom-actions>
            </x-layouts.card>
        </div>
        <div class="lg:col-span-2">
            <x-layouts.card title="Permissions">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="users_permissions__modal"
                        label="Edit"
                        icon="edit"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('users.permissions', 'edit', { id: {{ $user->id }}})"
                    />
                </x-slot:top-actions>

                @if ($userPermissions->isNotEmpty())
                    @foreach ($userPermissions as $section => $permissions)
                        <h3 class="text-lg font-semibold text-accent">{{ $section }}</h3>
                        <ul class="list-inside list-disc">
                            @foreach ($permissions as $permission)
                                <li>{{ $permission->label }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                @else
                    <p>N/A</p>
                @endif
            </x-layouts.card>
        </div>
    </div>

    <livewire:users.form />
    <livewire:users.permissions />
</div>
