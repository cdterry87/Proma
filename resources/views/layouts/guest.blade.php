<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main id="main" class="guest">
        <a href="{{ url('/') }}" class="logo guest__logo">{{ config('app.name') }}</a>

        <div class="guest__content">
            {{ $slot }}
        </div>
    </main>

    <x-toasts />

    @livewireScripts
</body>
</html>
