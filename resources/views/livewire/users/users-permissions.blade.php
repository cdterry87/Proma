<x-modals.base
    id="users_permissions__modal"
    title="Permissions Form"
    :subtitle="$user_name ? 'For ' . $user_name : null"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
            @foreach ($permissions as $permission)
                @if ($loop->first || $permission->section !== $permissions[$loop->index - 1]->section)
                    @if ($permission->section)
                        <div class="font-bold text-3xl mt-4 text-accent">{{ $permission->section }}</div>
                    @endif
                @endif
                <div class="flex items-center gap-2">
                    <x-inputs.checkbox
                        wire:model="selected_permissions.{{ $permission->id }}"
                        label="{{ $permission->label }}"
                        name="selected_permissions[]"
                        value="{{ $permission->id }}"
                    />
                </div>
            @endforeach
            <div class="mt-4">
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save"
                />
            </div>
        </div>
    </form>
</x-modals.base>
