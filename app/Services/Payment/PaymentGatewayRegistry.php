<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Gateways\BankTransferGateway;
use App\Services\Payment\Gateways\EpayGateway;
use App\Services\Payment\Gateways\PaypalGateway;
use App\Services\Payment\Gateways\RevolutGateway;
use App\Services\Payment\Gateways\StripeGateway;

final class PaymentGatewayRegistry
{
    /** @var array<string, PaymentGatewayInterface>|null */
    private static ?array $gateways = null;

    public static function get(string $slug): ?PaymentGatewayInterface
    {
        self::boot();
        $gw = self::$gateways[$slug] ?? null;
        if ($gw === null || !$gw->isConfigured()) {
            return null;
        }

        return $gw;
    }

    /** @return list<array{slug: string, label: string, automatic: bool}> */
    public static function availableForCheckout(): array
    {
        $map = [
            'bank' => ['label' => 'Банков превод', 'automatic' => false],
            'stripe' => ['label' => 'Карта (Stripe)', 'automatic' => true],
            'epay' => ['label' => 'ePay.bg', 'automatic' => true],
            'paypal' => ['label' => 'PayPal', 'automatic' => true],
            'revolut' => ['label' => 'Revolut Pay', 'automatic' => true],
        ];
        $out = [];
        foreach ($map as $slug => $meta) {
            if (self::get($slug) !== null) {
                $out[] = ['slug' => $slug, 'label' => $meta['label'], 'automatic' => $meta['automatic']];
            }
        }

        return $out;
    }

    public static function resolveMethodSlug(string $paymentMethod): string
    {
        $m = strtolower(trim($paymentMethod));
        if (in_array($m, ['bank', 'stripe', 'epay', 'paypal', 'revolut', 'card'], true)) {
            return $m === 'card' ? 'stripe' : $m;
        }
        if (preg_match('/epay/i', $paymentMethod)) {
            return 'epay';
        }
        if (preg_match('/paypal/i', $paymentMethod)) {
            return 'paypal';
        }
        if (preg_match('/revolut/i', $paymentMethod)) {
            return 'revolut';
        }
        if (preg_match('/карт|card|stripe/i', $paymentMethod)) {
            return 'stripe';
        }
        if (preg_match('/банк|превод/i', $paymentMethod)) {
            return 'bank';
        }

        return 'bank';
    }

    private static function boot(): void
    {
        if (self::$gateways !== null) {
            return;
        }
        self::$gateways = [
            'bank' => new BankTransferGateway(),
            'stripe' => new StripeGateway(),
            'epay' => new EpayGateway(),
            'paypal' => new PaypalGateway(),
            'revolut' => new RevolutGateway(),
        ];
    }
}
