<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Ce fichier définit les règles CORS pour ton backend Laravel (port 8000)
    | afin d'autoriser le frontend React (port 5173) à communiquer via Sanctum.
    |
    */

    'paths' => [
        'api/*',                  // Toutes les routes API
        'login', 'logout',       // Routes d'authentification personnalisées
        'register',              // Route d'inscription
        'sanctum/csrf-cookie',   // Nécessaire pour initialiser Sanctum
    ],

    'allowed_methods' => ['*'], // Autorise toutes les méthodes HTTP

    'allowed_origins' => [
        'http://localhost:5173', // Frontend React en dev (Vite)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Autorise tous les headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ⚠️ Obligatoire pour Sanctum (cookies)
];
