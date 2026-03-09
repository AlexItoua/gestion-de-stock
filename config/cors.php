<?php
// ============================================================
// config/cors.php
// Permet l'accès depuis le Canada ou partout (frontend mobile/web)
// ============================================================
return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => ['*'], // En prod: mettre l'URL exacte du frontend
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => false,
];
