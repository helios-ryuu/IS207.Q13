<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // QUAN TRỌNG: Không được để dấu '*', phải điền đúng link Frontend
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://172.24.176.1:5173', // IP mạng LAN của bạn (nếu dùng)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // QUAN TRỌNG: Phải là true vì frontend bạn đang bật withCredentials
    'supports_credentials' => true, 
];