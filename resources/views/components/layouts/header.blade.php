<div class="navbar bg-base-100">
    <div class="flex-1">
        <a class="btn btn-ghost text-xl">daisyUI</a>
    </div>
    <div class="flex-none gap-2">
        <div class="dropdown dropdown-end">
            <label
                tabindex="0"
                role="button"
                class="btn btn-ghost btn-circle"
            >
                <x-layouts.theme-controller />
            </label>
        </div>
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
                <li><a>Profile</a></li>
                <li>
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        x-data
                    >
                        @csrf
                        <a
                            href="{{ route('logout') }}"
                            @click.prevent="$root.submit();"
                        >
                            {{ __('Log Out') }}
                        </a>
                </li>
            </ul>
        </div>
    </div>
</div>
