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

        <!-- Solución simple para optimizar espacio en pantallas grandes -->
        <style>
            @media (min-width: 1280px) {
                /* Contenedor principal más ancho */
                .container-app {
                    max-width: 1440px !important;
                }

                /* Reducir padding vertical */
                .py-6, .py-8 {
                    padding-top: 1.5rem !important;
                    padding-bottom: 1.5rem !important;
                }

                /* Optimizar distribución de grids */
                .grid-cols-1, .grid-cols-2, .grid-cols-3 {
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
                }
            }

            @media (min-width: 1536px) {
                .container-app {
                    max-width: 1600px !important;
                }
            }

            /* Asegurar que tablas usen ancho completo */
            table, .table-responsive {
                width: 100% !important;
            }

            /* Permitir que el texto en celdas haga wrap */
            td, th {
                white-space: normal !important;
            }

            /* Prevenir scroll horizontal */
            html, body {
                overflow-x: hidden !important;
            }
        </style>

        <!-- Scripts y Styles -->
        @if (app()->environment('production'))
            @php
                $manifestPath = public_path('build/manifest.json');
                $cssFile = null;
                $jsFile = null;
                
                if (file_exists($manifestPath)) {
                    $manifest = json_decode(file_get_contents($manifestPath), true);
                    if (isset($manifest['resources/js/app.js'])) {
                        $cssFiles = $manifest['resources/js/app.js']['css'] ?? [];
                        $jsFile = 'build/' . ($manifest['resources/js/app.js']['file'] ?? '');
                        if (!empty($cssFiles)) {
                            $cssFile = 'build/' . $cssFiles[0];
                        }
                    }
                }
                
                // Fallback a los archivos que sabemos que existen en el servidor
                if (empty($cssFile) && file_exists(public_path('build/assets/app-Db9GbNfH.css'))) {
                    $cssFile = 'build/assets/app-Db9GbNfH.css';
                }
                
                if (empty($jsFile) && file_exists(public_path('build/assets/app-2m5xOVGx.js'))) {
                    $jsFile = 'build/assets/app-2m5xOVGx.js';
                }
            @endphp
            
            @if($cssFile)
                <link rel="stylesheet" href="{{ asset($cssFile) }}">
            @endif
            
            @if($jsFile)
                <script type="module" src="{{ asset($jsFile) }}"></script>
            @endif
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