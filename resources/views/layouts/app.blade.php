<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title', 'Websockets-Chat')</title>
        <link rel="stylesheet" href="{{ asset('css/milligram.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    </head>
    <body>
        <div id="app" class="page">
            <div class="topbar">
                @include('sections.topbar')
            </div>
            <div class="content container">
                @yield('content')
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('scripts')
    </body>
</html>