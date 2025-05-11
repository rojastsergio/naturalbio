<?php

return [
    'url' => env('APP_URL'),
    'port' => null,
    'defaults' => [],
    'routes' => null,
    'groups' => [
        'admin' => [
            'admin.*',
        ],
    ],
];