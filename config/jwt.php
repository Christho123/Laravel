<?php

return [
    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),
    'secret' => env('JWT_SECRET'),
    'access_ttl_minutes' => env('JWT_ACCESS_TTL_MINUTES', 15),
    'refresh_ttl_days' => env('JWT_REFRESH_TTL_DAYS', 30),
];
