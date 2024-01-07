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

            <x-rows.container>
                @if ($members->isNotEmpty())
                    @foreach ($members as $result)
                        <div class="flex items-center justify-between gap-2 px-4 py-2 bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span>
                                    {{ $result->user->name }}
                                </span>
                                @if ($result->manager)
                                    <span class="text-gray-400">(Manager)</span>
                                @endif
                            </div>
                            <div>
                                <x-confirms-delete wire:then="deleteMember({{ $result->id }})">
                                    <button
                                        class="rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out"
                                        alt="Delete"
                                        title="Delete"
                                    >
                                        <x-icons.delete class="h-6 w-6" />
                                    </button>
                                </x-confirms-delete>
                            </div>
                        </div>
                    @endforeach
                @else
                    <x-empty-results
                        title="No members found."
                        description="Use the form above to add a member."
                    />
                @endif
            </x-rows.container>
        </div>
    </x-drawer-form-container>
</x-drawer>
