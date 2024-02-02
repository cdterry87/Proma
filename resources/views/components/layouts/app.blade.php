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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body
    class="font-sans antialiased"
    style="visibility: hidden;"
>
    <div class="flex flex-col gap-6">
        <x-layouts.header />

        <main class="w-full max-w-6xl mx-auto px-6">
            {{ $slot }}
        </main>

        <x-layouts.footer />
    </div>

    <!-- Styles -->
    @livewireScripts

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
