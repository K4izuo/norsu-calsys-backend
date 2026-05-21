<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Settings tuned for Sanctum SPA (cookie) auth. `supports_credentials`
    | MUST be true so browsers attach the session + XSRF cookies on
    | cross-origin requests from the Next.js frontend.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
