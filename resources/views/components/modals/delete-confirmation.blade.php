@props(['id', 'label', 'action'])

<div>
    <x-modals.trigger
        id="{{ $id }}"
        label="{{ $label }}"
        icon="delete"
        class="btn-error btn-sm"
    />

    <x-modals.base
        id="{{ $id }}"
        title="{{ $label }}"
    >
        <div class="flex flex-col gap-6">
            <h3 class="font-bold text-xl">
                Are you sure you want to delete this record? This action will delete this record and all of its attached
                records.
            </h3>

            <div class="flex items-center gap-4">
                <x-inputs.button
                    class="btn-error"
                    label="Delete"
                    icon="delete"
                    wire:click.prevent="{{ $action }}"
                />

                <label
                    for="{{ $id }}"
                    wire:click="$dispatch('closeModal')"
                    class="btn btn-neutral"
                >
                    Cancel
                </label>
            </div>
        </div>
    </x-modals.base>
</div>
