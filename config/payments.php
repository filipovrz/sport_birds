<?php

declare(strict_types=1);

/** @return array<string, mixed> */
return [
    'eur_bgn_rate' => (float) ($_ENV['PAYMENT_EUR_BGN_RATE'] ?? '1.95583'),
    'stripe' => [
        'secret_key' => $_ENV['STRIPE_SECRET_KEY'] ?? '',
        'webhook_secret' => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
        'enabled' => ($_ENV['STRIPE_ENABLED'] ?? 'false') === 'true',
    ],
    'epay' => [
        'min' => $_ENV['EPAY_MIN'] ?? '',
        'secret' => $_ENV['EPAY_SECRET'] ?? '',
        'url' => $_ENV['EPAY_URL'] ?? 'https://www.epay.bg/',
        'enabled' => ($_ENV['EPAY_ENABLED'] ?? 'false') === 'true',
    ],
    'paypal' => [
        'client_id' => $_ENV['PAYPAL_CLIENT_ID'] ?? '',
        'secret' => $_ENV['PAYPAL_SECRET'] ?? '',
        'mode' => $_ENV['PAYPAL_MODE'] ?? 'sandbox',
        'enabled' => ($_ENV['PAYPAL_ENABLED'] ?? 'false') === 'true',
    ],
    'revolut' => [
        'api_secret' => $_ENV['REVOLUT_API_SECRET'] ?? '',
        'mode' => $_ENV['REVOLUT_MODE'] ?? 'sandbox',
        'enabled' => ($_ENV['REVOLUT_ENABLED'] ?? 'false') === 'true',
    ],
    'bank' => [
        'enabled' => true,
    ],
];
