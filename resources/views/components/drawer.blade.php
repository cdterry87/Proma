<div
    x-cloak
    x-data="{ isDrawerOpen: @entangle('isDrawerOpen') }"
    @keydown.window.escape="isDrawerOpen = false"
>
    <div
        x-cloak
        x-show="isDrawerOpen"
        class="opacity-50 fixed top-0 left-0 z-40 w-screen h-screen bg-black overflow-hidden"
        @click.prevent="
            isDrawerOpen = false
            $wire.closeDrawer()
        "
    ></div>

    <div
        x-cloak
        x-show="isDrawerOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-0 translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="bg-white dark:bg-gray-800 dark:text-gray-100 h-screen overflow-y-auto w-full md:w-2/3 xl:w-1/2 fixed top-0 right-0 transform duration-200 ease-in-out z-40 p-4 rounded-l-xl sm:p-10 border-l-4 dark:border-indigo-600"
    >
        {{ $slot }}
    </div>
</div>
