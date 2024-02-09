@props(['id', 'title' => null])

<div>
    <input
        type="checkbox"
        id="{{ $id }}"
        class="modal-toggle"
    />
    <div
        class="modal modal-bottom sm:modal-middle"
        role="dialog"
    >
        <div class="modal-box flex flex-col gap-4">
            <div class="flex items-center justify-between">

                <h3 class="font-bold text-2xl">
                    {{ $title ?? null }}
                </h3>
                <label
                    for="{{ $id }}"
                    class="modal-close cursor-pointer flex items-center gap-1 hover:brightness-75 transition duration-200 ease-in-out"
                    wire:click="$dispatch('closeModal')"
                >
                    <x-icons.close />
                </label>
            </div>

            <section>
                {{ $slot }}
            </section>
        </div>
        <label
            class="modal-backdrop"
            for="{{ $id }}"
            wire:click="$dispatch('closeModal')"
        >Close</label>
    </div>
</div>
