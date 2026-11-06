<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <div class="guest">
        <div class="guest__theme">
            <x-theme-toggle />
        </div>

        <header class="guest__brand">
            <a href="{{ url('/') }}" class="logo guest__logo">{{ config('app.name') }}</a>
            <p>Simplify your project management.</p>
        </header>

        <main id="main" @class(['guest__content', 'guest__content--wide' => $wide ?? false])>
            {{ $slot }}
        </main>

        <footer class="guest__footer">
            <p>
                &copy; {{ date('Y') }}. All rights reserved.
                <a href="{{ route('privacy') }}">Privacy Policy</a>
            </p>
            <p>Developed by <a href="https://chaseterry.com" rel="noopener">Chase Terry</a></p>
        </footer>
    </div>

    <x-toasts />

    @livewireScripts
</body>
</html>
