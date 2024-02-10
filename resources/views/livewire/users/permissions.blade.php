<x-modals.base
    id="users_permissions__modal"
    title="Permissions Form"
    :subtitle="$user_name ? 'For ' . $user_name : null"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
            <div>
                Permissions
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
