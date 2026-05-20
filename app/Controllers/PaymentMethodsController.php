<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CheckoutFlowService;
use App\Services\SettingsService;

final class PaymentMethodsController extends Controller
{
    public function index(): void
    {
        $methods = CheckoutFlowService::methodsForForms();
        $bankInstructions = trim(SettingsService::get('payment_instructions', '') ?? '');

        $this->view('payment.methods', [
            'methods' => $methods,
            'bankInstructions' => $bankInstructions,
            'gatewayLabels' => [
                'bank' => 'Банков превод',
                'stripe' => 'Кредитна/дебитна карта (Stripe)',
                'epay' => 'ePay.bg',
                'paypal' => 'PayPal',
                'revolut' => 'Revolut Pay',
            ],
        ], 'layouts.guest');
    }
}
