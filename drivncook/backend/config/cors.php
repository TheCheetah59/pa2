<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login', 'logout', 'register',
        'customer/login', 'customer/logout',
    ],

    'allowed_methods' => ['*'],

    // IMPORTANT: origins exactes quand supports_credentials=true
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],

    'max_age' => 0,

    // Nécessaire pour envoyer/recevoir les cookies
    'supports_credentials' => true,

    // Autoriser les cookies pour le front local
];
