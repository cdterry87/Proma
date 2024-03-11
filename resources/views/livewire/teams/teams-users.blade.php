<x-modals.base
    id="teams_users__modal"
    title="Team Members"
    :subtitle="$team_name ? 'For ' . $team_name : null"
>
    <form wire:submit.prevent="addMember">
        <x-alerts.container />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 w-full">
                <x-inputs.select
                    label="User"
                    name="user_id"
                    :options="$users"
                    wire:model="user_id"
                    default-option="Select a user"
                    required
                />
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Add Member"
                />
            </div>
        </div>
    </form>
</x-modals.base>
