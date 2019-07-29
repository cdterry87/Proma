<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="/css/auth.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/materialize/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
    <div id="main">
        <div id="app" class="container">
            <div class="row">
                <div class="col m8 offset-m2">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="/materialize/js/materialize.min.js"></script>
</body>
</html>
