<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Services\Payment\HttpClient;
use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayInterface;

final class PaypalGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'paypal';
    }

    public function isConfigured(): bool
    {
        $cfg = PaymentConfig::gateway('paypal');

        return trim((string) ($cfg['client_id'] ?? '')) !== ''
            && trim((string) ($cfg['secret'] ?? '')) !== '';
    }

    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array
    {
        $token = $this->accessToken();
        $api = $this->apiBase();
        $res = HttpClient::postJson($api . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $payment['public_token'],
                'description' => $payment['description'] ?? 'Best Sport Byrds',
                'amount' => [
                    'currency_code' => 'EUR',
                    'value' => number_format((float) $payment['amount_eur'], 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => $returnUrl . (str_contains($returnUrl, '?') ? '&' : '?') . 'gateway=paypal',
                'cancel_url' => $cancelUrl,
                'brand_name' => 'Best Sport Byrds',
                'user_action' => 'PAY_NOW',
            ],
        ], ['Authorization: Bearer ' . $token]);
        $data = json_decode($res['body'], true);
        $approve = null;
        foreach ($data['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approve = $link['href'];
                break;
            }
        }
        if ($approve === null) {
            throw new \RuntimeException('PayPal: липсва линк за плащане.');
        }

        return [
            'redirect_url' => $approve,
            'session_id' => $data['id'] ?? null,
        ];
    }

    public function verifyReturn(array $payment, array $query): ?string
    {
        $orderId = trim((string) ($query['token'] ?? ''));
        if ($orderId === '') {
            return null;
        }
        $access = $this->accessToken();
        $api = $this->apiBase();
        $res = HttpClient::post(
            $api . '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture',
            '{}',
            [
                'Authorization: Bearer ' . $access,
                'Content-Type: application/json',
            ]
        );
        $data = json_decode($res['body'], true);
        if (($data['status'] ?? '') === 'COMPLETED') {
            return $orderId;
        }

        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): ?array
    {
        $event = json_decode($rawBody, true);
        if (($event['event_type'] ?? '') !== 'CHECKOUT.ORDER.APPROVED'
            && ($event['event_type'] ?? '') !== 'PAYMENT.CAPTURE.COMPLETED') {
            return null;
        }
        $resource = $event['resource'] ?? [];
        $ref = $resource['purchase_units'][0]['reference_id']
            ?? $resource['custom_id']
            ?? '';

        return [
            'gateway_payment_id' => $resource['id'] ?? '',
            'payment_token' => $ref,
        ];
    }

    private function accessToken(): string
    {
        $cfg = PaymentConfig::gateway('paypal');
        $res = HttpClient::post(
            $this->apiBase() . '/v1/oauth2/token',
            'grant_type=client_credentials',
            [
                'Authorization: Basic ' . base64_encode($cfg['client_id'] . ':' . $cfg['secret']),
                'Content-Type: application/x-www-form-urlencoded',
            ]
        );
        $data = json_decode($res['body'], true);
        if (empty($data['access_token'])) {
            throw new \RuntimeException('PayPal: неуспешна автентикация.');
        }

        return $data['access_token'];
    }

    private function apiBase(): string
    {
        $mode = PaymentConfig::gateway('paypal')['mode'] ?? 'sandbox';

        return $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
