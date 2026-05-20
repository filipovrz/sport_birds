<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Services\Payment\HttpClient;
use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayInterface;

/** Revolut Merchant API — поръчка с redirect checkout. */
final class RevolutGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'revolut';
    }

    public function isConfigured(): bool
    {
        $cfg = PaymentConfig::gateway('revolut');

        return trim((string) ($cfg['api_secret'] ?? '')) !== '';
    }

    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array
    {
        $secret = (string) PaymentConfig::gateway('revolut')['api_secret'];
        $api = $this->apiBase();
        $amountMinor = (int) round((float) $payment['amount_eur'] * 100);
        $res = HttpClient::postJson($api . '/api/orders', [
            'amount' => $amountMinor,
            'currency' => 'EUR',
            'description' => $payment['description'] ?? 'Best Sport Byrds',
            'merchant_order_ext_ref' => $payment['public_token'],
            'redirect_url' => $returnUrl,
        ], [
            'Authorization: Bearer ' . $secret,
            'Revolut-Api-Version: 2023-09-01',
        ]);
        $data = json_decode($res['body'], true);
        $url = $data['checkout_url'] ?? $data['public_id'] ?? null;
        if ($url === null && !empty($data['id'])) {
            $url = $returnUrl . (str_contains($returnUrl, '?') ? '&' : '?')
                . 'gateway=revolut&order_id=' . rawurlencode((string) $data['id']);
        }
        if ($url === null) {
            throw new \RuntimeException('Revolut: неуспешно създаване на поръчка.');
        }
        if (!str_starts_with((string) $url, 'http')) {
            $url = 'https://checkout.revolut.com/payment-link/' . $url;
        }

        return [
            'redirect_url' => $url,
            'session_id' => $data['id'] ?? null,
        ];
    }

    public function verifyReturn(array $payment, array $query): ?string
    {
        $orderId = trim((string) ($query['order_id'] ?? ''));
        if ($orderId === '') {
            return null;
        }
        $secret = (string) PaymentConfig::gateway('revolut')['api_secret'];
        $res = HttpClient::get(
            $this->apiBase() . '/api/orders/' . rawurlencode($orderId),
            [
                'Authorization: Bearer ' . $secret,
                'Revolut-Api-Version: 2023-09-01',
            ]
        );
        $data = json_decode($res['body'], true);
        if (in_array(strtoupper((string) ($data['state'] ?? '')), ['COMPLETED', 'COMPLETED_CAPTURED'], true)) {
            return $orderId;
        }

        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): ?array
    {
        $event = json_decode($rawBody, true);
        if (($event['event'] ?? '') !== 'ORDER_COMPLETED') {
            return null;
        }
        $order = $event['order'] ?? [];

        return [
            'gateway_payment_id' => $order['id'] ?? '',
            'payment_token' => $order['merchant_order_ext_ref'] ?? '',
        ];
    }

    private function apiBase(): string
    {
        $mode = PaymentConfig::gateway('revolut')['mode'] ?? 'sandbox';

        return $mode === 'live'
            ? 'https://merchant.revolut.com'
            : 'https://sandbox-merchant.revolut.com';
    }
}
