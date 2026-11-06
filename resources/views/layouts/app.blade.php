<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <a href="#main" class="skip-link">Skip to content</a>

    <div class="shell" x-data="shell">
        <x-sidebar />

        <div class="shell__main">
            <header class="topbar">
                <x-button
                    variant="ghost"
                    icon="bars"
                    label="Toggle navigation"
                    class="topbar__menu"
                    aria-controls="sidebar"
                    x-bind:aria-expanded="open"
                    x-on:click="toggle"
                />
                <x-theme-toggle />
            </header>

            <main id="main" class="shell__content">
                {{ $slot }}
            </main>

            <footer class="footer">
                <span>&copy; {{ date('Y') }}. All rights reserved.</span>
                <a href="{{ url('/privacy-policy') }}" wire:navigate>Privacy Policy</a>
            </footer>
        </div>
    </div>

    <x-toasts />

    @livewireScripts
</body>
</html>
