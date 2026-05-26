<?php

return [
    'paths'                    => ['api/*'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => [
        env('FRONTEND_NEBULA_URL',  'http://localhost:3000'),
        env('FRONTEND_ADMIN_URL',   'http://localhost:3001'),
        env('FRONTEND_TICKETS_URL', 'http://localhost:3002'),
        env('FRONTEND_ACCESS_URL',  'http://localhost:3003'),
            'http://127.0.0.1:5502',  // desarrollo local
            'http://localhost:5502',
            'http://127.0.0.1:5500',   // desarrollo local
            'http://127.0.0.1:5501',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,
];
