<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="dark"
    class="dark"
>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Afacad&family=Hedvig+Letters+Serif:opsz@12..24&display=swap"
        rel="stylesheet"
    >

    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="/apple-touch-icon.png"
    >
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="/favicon-32x32.png"
    >
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="/favicon-16x16.png"
    >
    <link
        rel="manifest"
        href="/site.webmanifest"
    >

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>


<body
    class="font-sans antialiased"
    style="visibility: hidden;"
>

    <div class="drawer lg:drawer-open">
        <input
            id="drawer"
            type="checkbox"
            class="drawer-toggle"
        />
        <div class="drawer-content flex flex-col items-center justify-center">
            <div class="w-full flex flex-col gap-6 justify-between min-h-screen">
                <div class="flex flex-col gap-6">
                    <x-layouts.header />
                    <main class="w-full px-6">
                        {{ $slot }}
                    </main>
                </div>
                <x-layouts.footer />
            </div>
        </div>
        <div class="drawer-side">
            <label
                for="drawer"
                aria-label="close sidebar"
                class="drawer-overlay"
            ></label>
            <ul class="menu p-4 w-80 min-h-full text-2xl bg-base-300">
                <div class="py-4 flex flex-col gap-4">
                    <span class="logo text-4xl select-none px-2">
                        {{ config('app.name') }}
                    </span>
                    <div class="flex flex-col bg-base-100 p-2 rounded-lg px-4">
                        <span class="text-3xl text-gray-600">
                            Welcome,
                        </span>
                        <span class="text-2xl text-gray-400 font-bold">
                            {{ auth()->user()->name }}!
                        </span>
                    </div>
                </div>
                <li>
                    <a
                        href="{{ route('home') }}"
                        class="{{ request()->is('/') ? 'active' : '' }}"
                    >
                        <x-icons.home class="w-6 h-6" />
                        Home
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('projects') }}"
                        class="{{ request()->is('projects*') ? 'active' : '' }}"
                    >
                        <x-icons.projects class="w-6 h-6" />
                        Projects
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('issues') }}"
                        class="{{ request()->is('issues*') ? 'active' : '' }}"
                    >
                        <x-icons.issues class="w-6 h-6" />
                        Issues
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('clients') }}"
                        class="{{ request()->is('clients*') ? 'active' : '' }}"
                    >
                        <x-icons.clients class="w-6 h-6" />
                        Clients
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
                <li>
                    <a
                        href="{{ route('settings') }}"
                        class="{{ request()->is('settings*') ? 'active' : '' }}"
                    >
                        <x-icons.settings class="w-6 h-6" />
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
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    {{-- Prevent flash of unstyled content (FOUC) --}}
    <script>
        let domReady = (cb) => {
            document.readyState === 'interactive' || document.readyState === 'complete' ?
                cb() :
                document.addEventListener('DOMContentLoaded', cb);
        };
        domReady(() => {
            document.body.style.visibility = 'visible';
        });
    </script>
</body>

</html>
