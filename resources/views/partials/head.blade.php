<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ filled($title ?? null) ? $title.' | '.config('app.name') : config('app.name') }}</title>

<script nonce="{{ Vite::cspNonce() }}">
    (function () {
        var theme = null;

        try {
            theme = localStorage.getItem('theme');
        } catch (error) {}

        if (theme !== 'light' && theme !== 'dark') {
            theme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        document.documentElement.dataset.theme = theme;
    })();
</script>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
