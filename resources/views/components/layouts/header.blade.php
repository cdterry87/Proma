<div class="navbar bg-base-100">
    <div class="flex-1">
        <a
            href="{{ route('home') }}"
            class="btn btn-ghost text-3xl logo"
        >
            {{ config('app.name') }}
        </a>
    </div>
    <div class="flex-none gap-1">
        <label
            tabindex="0"
            role="button"
            class="btn btn-ghost btn-circle"
        >
            <x-layouts.theme-controller />
        </label>
        <a
            href="{{ route('notifications') }}"
            tabindex="0"
            role="button"
            class="btn btn-ghost btn-circle"
        >
            <div class="indicator">
                <x-icons.notifications />
                <span class="badge badge-xs badge-primary indicator-item"></span>
            </div>
        </a>
        <div class="dropdown dropdown-end">
            <div
                tabindex="0"
                role="button"
                class="btn btn-ghost btn-circle"
            >
                <x-icons.profile />
            </div>
            <ul
                tabindex="0"
                class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52"
            >
                <li>
                    <a
                        class="flex items-center gap-1"
                        href="{{ route('settings') }}"
                    >
                        <x-icons.settings />
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
                            class="flex items-center gap-1"
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
    </div>
</div>
