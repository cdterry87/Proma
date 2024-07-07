<div class="w-full">
    <div class="flex flex-col gap-4">
        <h1 class="font-bold text-3xl">Account Settings</h1>

        <x-alerts.container />

        {{-- Edit Name --}}
        <div
            x-data="{ isEditing: false }"
            class=" card card-compact bg-base-200"
        >
            <div class="card-body flex flex-col gap-2">
                <h2 class="font-bold text-lg text-gray-500 uppercase">Name</h2>
                <div
                    x-show="!isEditing"
                    class="flex items-center justify-between gap-4"
                >
                    <span class="text-2xl">
                        {{ $user->name }}
                    </span>
                    <span>
                        <x-inputs.button
                            icon="edit"
                            label="Edit"
                            class="btn-sm btn-primary w-full"
                            @click.prevent="isEditing = !isEditing"
                        />
                    </span>
                </div>
                <div
                    x-show="isEditing"
                    class="flex flex-col gap-4"
                >
                    <x-inputs.text
                        label="Edit Your Name"
                        name="name"
                        placeholder="Edit Your Name"
                        wire:model="name"
                        required
                    />
                    <div class="flex items-center gap-2">
                        <div>
                            <x-inputs.button
                                label="Save"
                                class="btn-sm btn-primary w-full"
                                wire:click.prevent="saveName"
                            />
                        </div>
                        <div>
                            <x-inputs.button
                                label="Cancel"
                                class="btn-sm btn-accent w-full"
                                @click.prevent="isEditing = !isEditing"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Email --}}
        <div
            x-data="{ isEditing: false }"
            class=" card card-compact bg-base-200"
        >
            <div class="card-body flex flex-col gap-2">
                <h2 class="font-bold text-lg text-gray-500 uppercase">Email Address</h2>
                <div
                    x-show="!isEditing"
                    class="flex items-center justify-between gap-4"
                >
                    <span class="text-2xl">
                        {{ $user->email }}
                    </span>
                    <span>
                        <x-inputs.button
                            icon="edit"
                            label="Edit"
                            class="btn-sm btn-primary w-full"
                            @click.prevent="isEditing = !isEditing"
                        />
                    </span>
                </div>
                <div
                    x-show="isEditing"
                    class="flex flex-col gap-4"
                >
                    <x-inputs.text
                        label="Edit Your Email"
                        name="email"
                        type="email"
                        placeholder="Edit Your Email"
                        wire:model="email"
                        required
                    />
                    <div class="flex items-center gap-2">
                        <div>
                            <x-inputs.button
                                label="Save"
                                class="btn-sm btn-primary w-full"
                                wire:click.prevent="saveEmail"
                            />
                        </div>
                        <div>
                            <x-inputs.button
                                label="Cancel"
                                class="btn-sm btn-accent w-full"
                                @click.prevent="isEditing = !isEditing"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Title --}}
        <div
            x-data="{ isEditing: false }"
            class=" card card-compact bg-base-200"
        >
            <div class="card-body flex flex-col gap-2">
                <h2 class="font-bold text-lg text-gray-500 uppercase">Title</h2>
                <div
                    x-show="!isEditing"
                    class="flex items-center justify-between gap-4"
                >
                    <span class="text-2xl">
                        {{ $user->title }}
                    </span>
                    <span>
                        <x-inputs.button
                            icon="edit"
                            label="Edit"
                            class="btn-sm btn-primary w-full"
                            @click.prevent="isEditing = !isEditing"
                        />
                    </span>
                </div>
                <div
                    x-show="isEditing"
                    class="flex flex-col gap-4"
                >
                    <x-inputs.text
                        label="Edit Your Title"
                        name="title"
                        placeholder="Edit Your Title"
                        wire:model="title"
                        required
                    />
                    <div class="flex items-center gap-2">
                        <div>
                            <x-inputs.button
                                label="Save"
                                class="btn-sm btn-primary w-full"
                                wire:click.prevent="saveTitle"
                            />
                        </div>
                        <div>
                            <x-inputs.button
                                label="Cancel"
                                class="btn-sm btn-accent w-full"
                                @click.prevent="isEditing = !isEditing"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div
            x-data="{ isEditing: false }"
            class="card card-compact bg-base-200"
        >
            <div class="card-body flex flex-col gap-4">
                <h2 class="font-bold text-lg text-gray-500 uppercase">Password</h2>
                <div
                    x-show="!isEditing"
                    class="flex items-center justify-between gap-4"
                >
                    <div class="text-2xl">
                        ********
                    </div>
                    <x-inputs.button
                        icon="lock"
                        label="Change Password"
                        class="btn-primary btn-sm"
                        @click.prevent="isEditing = !isEditing"
                    />
                </div>
                <div
                    x-show="isEditing"
                    class="flex flex-col gap-4"
                >
                    <div>
                        <x-inputs.text
                            label="Your New Password"
                            name="password"
                            type="password"
                            placeholder="Your New Password"
                            wire:model="password"
                            required
                        />
                    </div>
                    <div>
                        <x-inputs.text
                            label="Confirm Password"
                            name="password_confirmation"
                            type="password"
                            placeholder="Confirm Your Password"
                            wire:model="password_confirmation"
                            required
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <div>
                            <x-inputs.button
                                label="Save"
                                class="btn-sm btn-primary w-full"
                                wire:click.prevent="changePassword"
                            />
                        </div>
                        <div>
                            <x-inputs.button
                                label="Cancel"
                                class="btn-sm btn-accent w-full"
                                @click.prevent="isEditing = !isEditing"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
