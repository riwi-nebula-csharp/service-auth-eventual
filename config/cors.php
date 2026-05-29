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
            'http://127.0.0.1:5503',
            'http://127.0.0.1:5504',
            'http://127.0.0.1:5505',
            'http://127.0.0.1:5506',
            'http://127.0.0.1:5507',
            'http://127.0.0.1:5508',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,
];
