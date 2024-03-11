<x-modals.base
    id="users_form__modal"
    title="User Form"
>
    <form wire:submit.prevent="save">
        <x-alerts.container />

        <div class="flex flex-col gap-2 w-full">
            <div class="flex justify-end">
                <x-inputs.toggle
                    label="Active"
                    name="active"
                    id="active"
                    wire:model="active"
                />
            </div>
            <x-inputs.text
                label="Name"
                name="name"
                placeholder="Name"
                wire:model="name"
                required
                maxlength="255"
            />
            <x-inputs.text
                label="Email"
                name="email"
                type="email"
                placeholder="Email address"
                wire:model="email"
                required
                maxlength="255"
            />
            <x-inputs.text
                label="Title"
                name="title"
                placeholder="Title"
                wire:model="title"
                required
                maxlength="255"
            />
            @if (!$model_id)
                <x-inputs.text
                    label="Password"
                    name="password"
                    placeholder="Password"
                    type="password"
                    wire:model="password"
                    required
                />
            @endif
            <div class="mt-4">
                <x-inputs.button
                    class="btn-primary btn-block"
                    label="Save"
                />
            </div>
        </div>
    </form>
</x-modals.base>
