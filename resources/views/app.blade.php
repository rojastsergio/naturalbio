<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title inertia>{{ config('app.name', 'NaturalBIO') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Optimizaciones para maximizar espacio en pantallas grandes -->
        <style>
            /* Estilos agresivos para forzar el uso del espacio completo */
            .container-app, .max-w-7xl, .max-w-6xl, [class*="container"] {
                max-width: 100% !important;
                width: 100% !important;
            }
            
            /* Forzar elementos a usar el ancho completo */
            table, .table-responsive, .grid, main > div > div {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Permitir que el texto se ajuste en celdas de tabla */
            td, th {
                white-space: normal !important;
                word-break: break-word !important;
            }
            
            /* Prevenir scroll horizontal a toda costa */
            body, html, .main-content, main, .min-h-screen {
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }
        </style>

        <!-- Ziggy routes -->
        @routes

        <!-- Scripts -->
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>