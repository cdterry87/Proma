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

            <hr>

            <div class="flex flex-col gap-2 w-full">
                @if ($teamUsers->isNotEmpty())
                    @foreach ($teamUsers as $teamUser)
                        <div class="stats shadow bg-gray-200 dark:bg-gray-800">
                            <div class="stat">
                                <div class="stat-figure text-error">
                                    <button
                                        class="btn btn-ghost btn-circle"
                                        wire:click="deleteMember({{ $teamUser->id }})"
                                    >
                                        <x-icons.delete />
                                    </button>
                                </div>
                                <div class="text-lg text-primary">{{ $teamUser->user->name }}</div>
                                <div class="stat-desc">{{ $teamUser->user->email }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">No users in this team.</p>
                @endif
            </div>
        </div>
    </form>
</x-modals.base>
