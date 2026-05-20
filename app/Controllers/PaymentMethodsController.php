<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Services\Payment\GatewayException;
use App\Services\PaymentCheckoutService;
use App\Services\PaymentMethodsService;
use App\Services\SettingsService;
use App\Services\SubscriptionService;

final class PaymentMethodsController extends Controller
{
    public function index(): void
    {
        $this->view('payment.methods', [
            'methods' => PaymentMethodsService::catalog(false),
        ], 'layouts.guest');
    }

    public function show(string $slug): void
    {
        $method = PaymentMethodsService::find($slug);
        if ($method === null) {
            http_response_code(404);
            echo 'Начинът на плащане не е наличен.';
            exit;
        }

        $pending = Auth::check() ? PaymentCheckoutService::pendingPaymentsForUser((int) Auth::id()) : [];
        $plans = Auth::check() && App::isInstalled() ? SubscriptionService::plans() : [];

        $this->view('payment.method_show', [
            'method' => $method,
            'bankInstructions' => trim(SettingsService::get('payment_instructions', '') ?? ''),
            'pendingPayments' => $pending,
            'plans' => $plans,
            'checkoutUrl' => PaymentMethodsService::checkoutUrl($slug),
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function checkout(string $slug): void
    {
        try {
            PaymentCheckoutService::start($slug, [
                'plan_id' => (int) ($_GET['plan_id'] ?? $_POST['plan_id'] ?? 0),
                'payable_type' => trim((string) ($_GET['payable_type'] ?? $_POST['payable_type'] ?? '')),
                'payable_id' => (int) ($_GET['payable_id'] ?? $_POST['payable_id'] ?? 0),
                'payment_token' => trim((string) ($_GET['payment_token'] ?? $_POST['payment_token'] ?? '')),
            ]);
        } catch (GatewayException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/payment-methods/' . $slug);
        }
    }
}
