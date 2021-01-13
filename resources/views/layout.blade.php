<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="regression calculator, cubic regression, linear regression, coefficients, correlation, determination">
        <meta name="description" content="Regression calculator it's a web application for calculating regression coefficients and indexes of correlation and determination. The linear, square and cubic regressions are available. The least square method is used for calculation.">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script src="{{asset('js/jquery-3.3.1.js')}}" type="application/javascript"></script>
        <script src="{{asset('js/chart.js')}}" type="application/javascript"></script>

        <!-- Styles -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/reset.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/chart.css') }}" rel="stylesheet">

    </head>

    <body>
        @yield('content')

        <footer class="footer">
            <div class="container">
                &copy; IT-EXPERIENCE
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
