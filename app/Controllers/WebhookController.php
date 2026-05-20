<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PaymentService;

final class WebhookController extends Controller
{
    public function stripe(): void
    {
        $this->handle('stripe');
    }

    public function epay(): void
    {
        $this->handle('epay');
    }

    public function paypal(): void
    {
        $this->handle('paypal');
    }

    public function revolut(): void
    {
        $this->handle('revolut');
    }

    private function handle(string $gateway): void
    {
        $raw = file_get_contents('php://input') ?: '';
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
                $headers[$name] = (string) $v;
            }
        }
        $ok = PaymentService::handleWebhook($gateway, $raw, $headers);
        http_response_code($ok ? 200 : 400);
        echo $ok ? 'OK' : 'IGNORED';
        exit;
    }
}
