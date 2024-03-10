<div>
    <div class="navbar bg-base-300">
        <div class="w-full max-w-6xl mx-auto">
            <div class="flex-1 flex items-center gap-1">
                <a
                    href="{{ route('home') }}"
                    class="logo btn btn-ghost text-3xl"
                >
                    {{ config('app.name') }}
                </a>
                <x-layouts.navigation class="hidden sm:flex" />
            </div>
            <div class="flex-none">
                <label
                    tabindex="0"
                    role="button"
                    class="btn btn-ghost btn-circle"
                >
                    <x-layouts.theme-controller />
                </label>
                <a
                    href="{{ route('notifications') }}"
                    class="btn btn-ghost btn-circle relative"
                >
                    <x-icons.notifications />
                    <span class="badge badge-primary badge-xs absolute top-1 right-1"></span>
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
                        class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-300 rounded-box w-52 z-50"
                    >
                        <li>
                            <a
                                class="flex items-center gap-1 w-full"
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
                                    class="flex items-center gap-1 w-full"
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
    </div>
    <div class="bg-base-300 w-full flex items-center justify-center sm:hidden">
        <x-layouts.navigation />
    </div>
</div>
