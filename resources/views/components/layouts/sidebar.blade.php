<div class="p-4 w-80 bg-base-100 text-base-content">
    <div class="flex justify-end mb-4 lg:hidden">
        <label
            for="sidebar"
            class="drawer-overlay cursor-pointer"
        >
            <x-icons.close class="w-6 h-6" />
        </label>
    </div>
    <img
        src="{{ asset('images/logo.svg') }}"
        alt="Logo"
        title="Logo"
        class="w-full my-4 lg:my-0"
    >
    <div class="w-full p-4 bg-base-200 rounded-xl my-4">
        <h3 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h3>
        <p class="text-secondary font-semibold">
            {{ auth()->user()->title }}
        </p>
        <div class="flex items-center gap-2 text-sm mt-2 mb-4">
            <a
                href="{{ route('settings') }}"
                class="flex items-center gap-4"
            >
                Settings
            </a>
            <span>|</span>
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="inline-block"
            >
                @csrf
                <a
                    href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                    class="flex items-center gap-4"
                >
                    Logout
                </a>
            </form>
        </div>
    </div>
    <ul class="menu">
        <li>
            <a
                href="{{ route('home') }}"
                class="{{ request()->is('home') ? 'active' : '' }}"
            >
                {{-- <x-icons.home class="w-6 h-6" /> --}}
                Home
            </a>
        </li>

        <li>
            <a
                href="{{ route('notifications') }}"
                class="{{ request()->is('notifications*') ? 'active' : '' }}"
            >
                <x-icons.notifications class="w-6 h-6" />
                Notifications
            </a>
        </li>
    </ul>
</div>
