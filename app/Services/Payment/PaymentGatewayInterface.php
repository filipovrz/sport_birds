<?php

declare(strict_types=1);

namespace App\Services\Payment;

/** @phpstan-type CheckoutResult array{redirect_url?: string, html?: string, session_id?: string} */
interface PaymentGatewayInterface
{
    public function slug(): string;

    public function isConfigured(): bool;

    /** @param array<string, mixed> $payment */
    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array;

    /** @param array<string, mixed> $payment */
    public function verifyReturn(array $payment, array $query): ?string;

    /** Verify webhook/callback payload; return gateway payment id or null. */
    public function verifyWebhook(string $rawBody, array $headers): ?array;
}
