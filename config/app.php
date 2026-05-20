<?php

return [
    'name' => 'Best Sport Byrds',
    'version' => '3.0.0',
    'phase' => 3,
    'tagline' => 'Професионално управление на спортни птици',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'timezone' => 'Europe/Sofia',
    'locale' => 'bg',
    'free_bird_limit' => 5,
    'free_loft_limit' => 1,
];
