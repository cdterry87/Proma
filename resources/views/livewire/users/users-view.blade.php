<div>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <div class="flex flex-col gap-8">

                <x-layouts.card title="User Details">
                    <x-slot:top-actions>
                        <x-modals.trigger
                            id="users_form__modal"
                            label="Edit"
                            icon="edit"
                            class="btn-primary btn-sm"
                            wire:click="$dispatchTo('users.users-form', 'edit', { id: {{ $user->id }}})"
                        />
                    </x-slot:top-actions>

                    <div class="flex flex-col gap-4">
                        <x-alerts.container />

                        <x-elements.badge
                            label="{{ $user->active ? 'Active' : 'Inactive' }}"
                            class="{{ $user->active ? 'badge-success' : 'badge-error' }}"
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
                <x-layouts.card title="User Stats">
                    <div class="stats shadow">
                        <div class="stat">
                            <div class="stat-figure text-primary">
                                <x-icons.projects />
                            </div>
                            <div class="stat-title">Assigned Projects</div>
                            <div class="stat-value text-primary">0</div>
                            <div class="stat-desc text-accent">0 Projects Complete</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <x-icons.issues />
                            </div>
                            <div class="stat-title">Assigned Issues</div>
                            <div class="stat-value text-secondary">0</div>
                            <div class="stat-desc text-accent">0 Issues Closed</div>
                        </div>
                    </div>
                </x-layouts.card>
            </div>
        </div>
        <div class="lg:col-span-2">
            <x-layouts.card title="User Permissions">
                <x-slot:top-actions>
                    <x-modals.trigger
                        id="users_permissions__modal"
                        label="Edit"
                        icon="edit"
                        class="btn-primary btn-sm"
                        wire:click="$dispatchTo('users.users-permissions', 'edit', { id: {{ $user->id }}})"
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
                    <p>User has no permissions.</p>
                @endif
            </x-layouts.card>
        </div>
    </div>

    <livewire:users.users-form />
    <livewire:users.users-permissions />
</div>
