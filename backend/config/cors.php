<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    // QUAN TRỌNG: Không được để dấu '*', phải điền đúng link Frontend
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://172.24.176.1:5173', // IP mạng LAN của bạn (nếu dùng)
        // Cloudflare domains
        'https://vietmarket.helios.id.vn',
        'https://api.vietmarket.helios.id.vn',
    ],

    'allowed_origins_patterns' => [
        // Match tất cả subdomain của helios.id.vn qua Cloudflare
        '/^https:\/\/([a-zA-Z0-9-]+\.)*helios\.id\.vn$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // QUAN TRỌNG: Phải là true vì frontend bạn đang bật withCredentials
    'supports_credentials' => true,
];