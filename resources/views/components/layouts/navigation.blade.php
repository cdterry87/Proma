<ul {{ $attributes->merge([
    'class' => 'menu menu-horizontal px-1',
]) }}>
    <li>
        <a
            href="{{ route('projects') }}"
            class="flex items-center gap-1"
        >
            <x-icons.projects />
            <span class="hidden md:block">
                Projects
            </span>
        </a>
    </li>
    <li>
        <a
            href="{{ route('issues') }}"
            class="flex items-center gap-1"
        >
            <x-icons.issues />
            <span class="hidden md:block">
                Issues
            </span>
        </a>
    </li>
    <li>
        <a
            href="{{ route('clients') }}"
            class="flex items-center gap-1"
        >
            <x-icons.clients />
            <span class="hidden md:block">
                Clients
            </span>
        </a>
    </li>
    <li>
        <details>
            <summary class="flex items-center gap-1">
                <x-icons.admin />
                <span class="hidden md:block">
                    Administration
                </span>
            </summary>
            <ul class="p-2 bg-base-300 z-50">
                <li>
                    <a
                        href="{{ route('users') }}"
                        class="flex items-center gap-1"
                    >
                        <x-icons.users />
                        Users
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('teams') }}"
                        class="flex items-center gap-1"
                    >
                        <x-icons.teams />
                        Teams
                    </a>
                </li>
            </ul>
        </details>
    </li>
</ul>
