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
        <x-layouts.side-bar />
    </div>

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
