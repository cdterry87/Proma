<div class="flex flex-col gap-4">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <h1 class="font-bold text-4xl text-white">Manage Users</h1>
        <x-button
            wire:click.prevent="openDrawer"
            class="flex items-center gap-1"
        >
            Add User
            <x-icons.plus class="h-4 w-4" />
        </x-button>
    </div>

    {{-- Filter, Search, Sort --}}
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between lg:gap-4">
        <div class="flex items-center gap-2">
            <x-button class="flex items-center gap-1">
                Filter
                <x-icons.filter class="h-4 w-4" />
            </x-button>
            <x-button class="flex items-center gap-1">
                Sort
                <x-icons.sort class="h-4 w-4" />
            </x-button>
        </div>
        <div>
            <x-search
                placeholder="Search..."
                wire:model.debounce.500ms="search"
            />
        </div>
    </div>

    {{-- Content --}}
    <div>
        <table class="w-full text-white">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr class="dark:text-gray-400 dark:bg-gray-800">
                        <td>
                            <img
                                class="h-16 w-16 rounded-full object-cover"
                                src="{{ $result->profile_photo_url }}"
                                alt="{{ $result->name }}"
                            />
                        </td>
                        <td>{{ $result->name }}</td>
                        <td>{{ $result->email }}</td>
                        <td>
                            {{ $result->phone }}
                            @if ($result->phone_ext)
                                (Ext. {{ $result->phone_ext }})
                            @endif
                        </td>
                        <td>{{ $result->title }}</td>
                        <td>
                            <button
                                class="rounded-full p-2 hover:bg-indigo-600 transition duration-200 ease-in-out"
                                wire:click.prevent="edit({{ $result->id }})"
                            >
                                <x-icons.edit class="h-6 w-6" />
                            </button>
                            <button
                                class="rounded-full p-2 hover:bg-indigo-600 transition duration-200 ease-in-out"
                                wire:click.prevent="delete({{ $result->id }})"
                            >
                                <x-icons.delete class="h-6 w-6" />
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Drawer --}}
    <x-drawer>
        <x-drawer-form-container
            title="User Form"
            primary-label="Save"
            primary-method="save"
            secondary-label="Cancel"
            secondary-method="closeDrawer"
        >
            <form class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <x-label
                        for="name"
                        :value="__('Name')"
                    />
                    <x-input
                        id="name"
                        class="w-full"
                        type="text"
                        wire:model.defer="name"
                        autofocus
                    />
                    <x-input-error for="name" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-label
                        for="title"
                        :value="__('Title')"
                    />
                    <x-input
                        id="title"
                        class="w-full"
                        type="text"
                        wire:model.defer="title"
                    />
                    <x-input-error for="title" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-label
                        for="email"
                        :value="__('Email')"
                    />
                    <x-input
                        id="email"
                        class="w-full"
                        type="email"
                        wire:model.defer="email"
                    />
                    <x-input-error for="email" />
                </div>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1">
                        <div class="flex flex-col gap-1">
                            <x-label
                                for="phone"
                                :value="__('Phone')"
                            />
                            <x-input
                                id="phone"
                                class="w-full"
                                type="text"
                                wire:model.defer="phone"
                            />
                            <x-input-error for="phone" />
                        </div>
                    </div>
                    <div>
                        <div class="flex flex-col gap-1">
                            <x-label
                                for="phone_ext"
                                :value="__('Phone Ext')"
                            />
                            <x-input
                                id="phone_ext"
                                class="w-full"
                                type="text"
                                wire:model.defer="phone_ext"
                            />
                            <x-input-error for="phone_ext" />
                        </div>
                    </div>
                </div>

                @if (!$model_id)
                    <div class="flex flex-col gap-1">
                        <x-label
                            for="password"
                            :value="__('Password')"
                        />
                        <x-input
                            id="password"
                            class="w-full"
                            type="password"
                            wire:model.defer="password"
                        />
                        <x-input-error for="password" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <x-label
                            for="password_confirmation"
                            :value="__('Confirm Password')"
                        />
                        <x-input
                            id="password_confirmation"
                            class="w-full"
                            type="password"
                            wire:model.defer="password_confirmation"
                        />
                        <x-input-error for="password_confirmation" />
                    </div>
                @endif
            </form>
        </x-drawer-form-container>
    </x-drawer>
</div>
