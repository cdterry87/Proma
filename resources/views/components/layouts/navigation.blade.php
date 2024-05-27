<ul {{ $attributes->merge([
    'class' => 'flex items-center justify-between',
]) }}>
    <li>
        <a
            href="{{ route('projects') }}"
            class="flex items-center gap-1 {{ request()->routeIs('projects') || request()->routeIs('projects.*') ? 'active' : '' }}"
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
            class="flex items-center gap-1 {{ request()->routeIs('issues') || request()->routeIs('issues.*') ? 'active' : '' }}"
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
            class="flex items-center gap-1 {{ request()->routeIs('clients') || request()->routeIs('clients.*') ? 'active' : '' }}"
        >
            <x-icons.clients />
            <span class="hidden md:block">
                Clients
            </span>
        </a>
    </li>
</ul>
