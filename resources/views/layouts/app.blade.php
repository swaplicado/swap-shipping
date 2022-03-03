<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'swap') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-dark" style="background-color: #0f2e52;">
            <div class="container-fluid">
                <a class="navbar-brand" href="/logoutFromVerify">
                    <img src="{{ asset('img/svg/orange.svg') }}" alt="" width="200" height="40" class="d-inline-block align-text-top">
                </a>
            </div>
        </nav>
        <br>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
