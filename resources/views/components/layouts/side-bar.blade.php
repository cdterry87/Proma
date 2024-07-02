<div class="drawer-side">
    <label
        for="drawer"
        aria-label="close sidebar"
        class="drawer-overlay"
    ></label>
    <ul class="menu p-4 w-80 min-h-full text-2xl bg-base-300">
        <div class="pb-4 flex flex-col gap-4">
            <div class="logo text-4xl select-none px-2">
                {{ config('app.name') }}
            </div>
            <div class="flex flex-col bg-base-100 p-2 rounded-lg px-4">
                <div class="text-sm text-neutral dark:text-gray-400 uppercase">
                    Welcome,
                </div>
                <div class="text-xl text-primary font-bold uppercase">
                    {{ auth()->user()->name }}!
                </div>
                <div class="text-sm text-secondary font-semibold uppercase italic text-right">
                    {{ auth()->user()->title }}
                </div>
            </div>
        </div>
        <li>
            <a
                href="{{ route('home') }}"
                class="{{ request()->is('/') ? 'active' : '' }}"
            >
                <x-icons.home class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Home
            </a>
        </li>
        <li>
            <a
                href="{{ route('projects') }}"
                class="{{ request()->is('projects*') ? 'active' : '' }}"
            >
                <x-icons.projects class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Projects
            </a>
        </li>
        <li>
            <a
                href="{{ route('issues') }}"
                class="{{ request()->is('issues*') ? 'active' : '' }}"
            >
                <x-icons.issues class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Issues
            </a>
        </li>
        <li>
            <a
                href="{{ route('clients') }}"
                class="{{ request()->is('clients*') ? 'active' : '' }}"
            >
                <x-icons.clients class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Clients
            </a>
        </li>
        <li>
            <a
                href="{{ route('notifications') }}"
                class="{{ request()->is('notifications*') ? 'active' : '' }}"
            >
                <x-icons.notifications class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Notifications
            </a>
        </li>
        <li>
            <a
                href="{{ route('settings') }}"
                class="{{ request()->is('settings*') ? 'active' : '' }}"
            >
                <x-icons.settings class="w-6 h-6 text-gray-500 dark:text-gray-600" />
                Settings
            </a>
        </li>
        <li>
            <form
                method="POST"
                action="{{ route('logout') }}"
                x-data
            >
                @csrf
                <a
                    class="flex items-center gap-1 w-full text-error"
                    href="{{ route('logout') }}"
                    @click.prevent="$root.submit();"
                >
                    <x-icons.logout />
                    {{ __('Log Out') }}
                </a>
            </form>
        </li>
    </ul>
</div>
