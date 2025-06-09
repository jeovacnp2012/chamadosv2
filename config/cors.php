<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => ['*'], // ou ['http://localhost:8081'] se quiser restringir
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,

    'allowed_methods' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

];
