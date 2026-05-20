<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Session;
use App\Services\Payment\PaymentGatewayRegistry;

final class CheckoutFlowService
{
    /**
     * Създава плащане и пренасочва към банка / gateway.
     *
     * @return never
     */
    public static function start(
        string $payableType,
        int $payableId,
        float $amountEur,
        string $description,
        string $paymentMethodPost
    ): void {
        $slug = PaymentGatewayRegistry::resolveMethodSlug($paymentMethodPost);
        $payment = PaymentService::create(
            (int) Auth::id(),
            $payableType,
            $payableId,
            $amountEur,
            $slug,
            $description
        );
        if ($slug === 'bank') {
            header('Location: /payment/bank/' . $payment['public_token']);
            exit;
        }
        header('Location: /payment/go/' . $payment['public_token']);
        exit;
    }

    public static function paymentMethodFromPost(): string
    {
        return trim((string) ($_POST['payment_method'] ?? 'bank'));
    }

    /** @return list<array{slug: string, label: string, automatic: bool}> */
    public static function methodsForForms(): array
    {
        $configured = PaymentGatewayRegistry::availableForCheckout();
        if ($configured !== []) {
            return $configured;
        }

        return [
            ['slug' => 'bank', 'label' => 'Банков превод', 'automatic' => false],
        ];
    }
}
