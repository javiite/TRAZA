<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Permite tu frontend de Vite
    'allowed_origins' => [
        'http://127.0.0.1:5173',
        'http://localhost:5173',
    ],

    // Alternativa flexible si cambias de puerto:
    // 'allowed_origins' => [],
    // 'allowed_origins_patterns' => ['#^http://(localhost|127\.0\.0\.1):517\d$#'],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    // Para tokens Bearer, déjalo en false (cookies no necesarias)
    'supports_credentials' => false,
];
