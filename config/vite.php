<?php

return [
    'paths' => [
        'build' => '/naturalbiosolutions/build',
    ],
    'manifest' => env('VITE_MANIFEST_PATH', public_path('build/manifest.json')),
];