<?php

return [
    'name' => 'Best Sport Byrds',
    'tagline' => 'Професионално управление на спортни птици',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'timezone' => 'Europe/Sofia',
    'locale' => 'bg',
    'free_bird_limit' => 10,
    'free_loft_limit' => 1,
];
