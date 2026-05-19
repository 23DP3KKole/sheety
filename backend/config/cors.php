<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL'),
        'https://sheety-mu.vercel.app', // Your main stable Vercel URL
    ],

    // This dynamically allows ANY preview URL Vercel generates for your project
    'allowed_origins_patterns' => [
        '/^https:\/\/sheety-.*-23dp3kkoles-projects\.vercel\.app$/'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];