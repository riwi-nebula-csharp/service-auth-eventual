<?php

return [
    'secret'     => env('JWT_SECRET'),
    'expiration' => env('JWT_EXPIRATION', 480),
    'issuer'     => env('JWT_ISSUER', 'service.auth.nebula.andrescortes.dev'),
];