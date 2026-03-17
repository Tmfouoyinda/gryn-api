<?php

/**
 * Configuration CORS pour Laravel.
 * Copiez ce contenu dans config/cors.php de votre projet Laravel.
 *
 * Pour que le frontend (Vite sur localhost:5173) puisse appeler l'API,
 * assurez-vous que 'allowed_origins' contient bien votre URL frontend.
 */

return [
    'paths'                    => ['api/*'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => [env('FRONTEND_URL', 'http://localhost:5173')],
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true,
];
