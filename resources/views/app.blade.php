<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts y Styles -->
        @if (app()->environment('production'))
            <!-- Estilos para producción -->
            <link rel="stylesheet" href="{{ asset('assets/app-BPm-YtUm.css') }}">
            <!-- Scripts para producción -->
            <script type="module" src="{{ asset('assets/app-CKnLOSKh.js') }}"></script>
        @else
            <!-- Usar Vite en desarrollo -->
            @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @endif

        @routes
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>