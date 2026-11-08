<?php

declare(strict_types=1);

return [
    'app' => [
        'base_path' => dirname(__DIR__),
        'site_url' => '/',
        'template_path' => 'resources/views',
        'debug' => env('APP_DEBUG', false),
    ],
];
