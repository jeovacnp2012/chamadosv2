<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => ['http://127.0.0.1:3000'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,

    'allowed_methods' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

];
