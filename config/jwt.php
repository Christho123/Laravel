<?php

return [
    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),
    'secret' => env('JWT_SECRET'),
    'refresh_ttl_days' => env('JWT_REFRESH_TTL_DAYS', 30),
];
