@props(['users', 'members'])

<x-drawer>
    <x-drawer-form-container
        title="Manage Members"
        primary-label="Add Member"
        primary-method="addMember"
        secondary-label="Cancel"
        secondary-method="closeMembersDrawer"
    >
        <div class="flex flex-col gap-8">

            <form class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <x-label
                        for="user_id"
                        :value="__('Add user to team')"
                    />
                    <x-select
                        id="user_id"
                        class="w-full"
                        type="text"
                        wire:model="user_id"
                    >
                        @if ($users && $users->isNotEmpty())
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        @else
                            <option
                                value=""
                                disabled
                            >No users found.</option>
                        @endif
                    </x-select>
                    <x-input-error for="user_id" />
                </div>
                <div class="flex justify-end">
                    <div class="flex items-center gap-2">
                        <x-label for="manager">
                            Manager?
                        </x-label>
                        <x-checkbox
                            id="manager"
                            wire:model="manager"
                        />
                    </div>
                </div>
            </form>

            <x-hr />

            <x-rows.teams-members :results="$members" />
        </div>
    </x-drawer-form-container>
</x-drawer>
