<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Services\Payment\HttpClient;
use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayInterface;

final class StripeGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'stripe';
    }

    public function isConfigured(): bool
    {
        $cfg = PaymentConfig::gateway('stripe');

        return trim((string) ($cfg['secret_key'] ?? '')) !== '';
    }

    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array
    {
        $secret = (string) PaymentConfig::gateway('stripe')['secret_key'];
        $amountCents = (int) round((float) $payment['amount_eur'] * 100);
        if ($amountCents < 50) {
            $amountCents = 50;
        }
        $successUrl = $returnUrl . (str_contains($returnUrl, '?') ? '&' : '?')
            . 'gateway=stripe&session_id={CHECKOUT_SESSION_ID}';
        $params = http_build_query([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => $payment['public_token'],
            'line_items[0][price_data][currency]' => 'eur',
            'line_items[0][price_data][unit_amount]' => $amountCents,
            'line_items[0][price_data][product_data][name]' => $payment['description'] ?? 'Best Sport Byrds',
            'line_items[0][quantity]' => 1,
            'metadata[payment_token]' => $payment['public_token'],
            'metadata[payable_type]' => $payment['payable_type'],
            'metadata[payable_id]' => (string) $payment['payable_id'],
        ]);
        $res = HttpClient::post(
            'https://api.stripe.com/v1/checkout/sessions',
            $params,
            ['Authorization: Bearer ' . $secret]
        );
        if ($res['code'] < 200 || $res['code'] >= 300) {
            throw new \RuntimeException('Stripe: ' . self::apiError($res['body']));
        }
        $data = json_decode($res['body'], true);
        if (empty($data['url'])) {
            throw new \RuntimeException('Stripe: липсва URL за плащане.');
        }

        return [
            'redirect_url' => $data['url'],
            'session_id' => $data['id'] ?? null,
        ];
    }

    public function verifyReturn(array $payment, array $query): ?string
    {
        $sessionId = trim((string) ($query['session_id'] ?? $payment['gateway_session_id'] ?? ''));
        if ($sessionId === '') {
            return null;
        }
        $secret = (string) PaymentConfig::gateway('stripe')['secret_key'];
        $res = HttpClient::get(
            'https://api.stripe.com/v1/checkout/sessions/' . rawurlencode($sessionId),
            ['Authorization: Bearer ' . $secret]
        );
        $data = json_decode($res['body'], true);
        if (($data['payment_status'] ?? '') === 'paid') {
            return $sessionId;
        }

        return null;
    }

    private static function apiError(string $body): string
    {
        $data = json_decode($body, true);

        return (string) ($data['error']['message'] ?? 'неуспешно API извикване');
    }

    public function verifyWebhook(string $rawBody, array $headers): ?array
    {
        $whSecret = (string) PaymentConfig::gateway('stripe')['webhook_secret'];
        if ($whSecret === '') {
            return null;
        }
        $sig = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? '';
        if ($sig === '' || !$this->verifyStripeSignature($rawBody, $sig, $whSecret)) {
            return null;
        }
        $event = json_decode($rawBody, true);
        $type = $event['type'] ?? '';
        if ($type === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];

            return [
                'gateway_payment_id' => $session['id'] ?? '',
                'payment_token' => $session['metadata']['payment_token'] ?? $session['client_reference_id'] ?? '',
            ];
        }

        return null;
    }

    private function verifyStripeSignature(string $payload, string $sigHeader, string $secret): bool
    {
        $parts = [];
        foreach (explode(',', $sigHeader) as $el) {
            [$k, $v] = array_map('trim', explode('=', $el, 2) + [null, null]);
            if ($k !== null && $v !== null) {
                $parts[$k][] = $v;
            }
        }
        $timestamp = $parts['t'][0] ?? '';
        $signatures = $parts['v1'] ?? [];
        if ($timestamp === '' || $signatures === []) {
            return false;
        }
        $signed = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signed, $secret);
        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }
}
