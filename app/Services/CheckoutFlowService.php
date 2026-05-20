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
        try {
            PaymentCheckoutService::start($slug, [
                'payable_type' => $payableType,
                'payable_id' => $payableId,
                'payment_token' => $payment['public_token'],
            ]);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            header('Location: /payment/status/' . $payment['public_token']);
            exit;
        }
    }

    public static function paymentMethodFromPost(): string
    {
        return trim((string) ($_POST['payment_method'] ?? 'bank'));
    }

    /** @return list<array{slug: string, label: string, automatic: bool}> */
    public static function methodsForForms(): array
    {
        $out = [];
        foreach (PaymentMethodsService::catalog(true) as $m) {
            $out[] = [
                'slug' => $m['slug'],
                'label' => $m['label'],
                'automatic' => $m['automatic'],
            ];
        }

        return $out !== [] ? $out : [
            ['slug' => 'bank', 'label' => 'Банков превод', 'automatic' => false],
        ];
    }
}
