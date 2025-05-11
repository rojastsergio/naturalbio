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