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