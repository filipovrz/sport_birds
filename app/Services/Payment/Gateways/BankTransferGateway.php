<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentGatewayInterface;

final class BankTransferGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'bank';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array
    {
        return ['redirect_url' => $returnUrl];
    }

    public function verifyReturn(array $payment, array $query): ?string
    {
        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): ?array
    {
        return null;
    }
}
