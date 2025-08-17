<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Development Only Routes
    |--------------------------------------------------------------------------
    |
    | Routes listed here are intended solely for local development and
    | debugging. They should never be exposed in production environments.
    | Toggle their availability using the DEBUG_ROUTES environment variable.
    |
    */
    'enabled' => env('DEBUG_ROUTES', false),

    'routes' => [
        '/test' => 'Basic API connectivity check',
    ],
];
