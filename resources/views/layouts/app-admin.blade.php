<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful voting app">
    <link rel="shortcut icon" href="./images/favicon.png">
    
    <title>iVote App</title>
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=3.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=3.2.0">
    @yield('css')
</head>

<body class="nk-body bg-white has-sidebar ">
    <div class="nk-app-root">
    </div>
    <div class="nk-main">
        @include('layouts.partials.sidebar')
        @yield('content')
    </div>
    </div>

    <script src="{{ asset('assets/js/bundle.js?ver=3.2.0') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=3.2.0') }}"></script>
    @yield('js')
</body>
</html>
