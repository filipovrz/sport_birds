<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\Payment\PaymentGatewayRegistry;
use App\Services\PaymentService;
use App\Services\SettingsService;

final class PaymentController extends Controller
{
    public function bank(string $token): void
    {
        $payment = PaymentService::findByToken($token);
        if (!$payment) {
            http_response_code(404);
            echo 'Плащането не е намерено.';
            exit;
        }
        PaymentService::assertOwner($payment);
        if (($payment['method'] ?? '') !== 'bank') {
            $this->redirect('/payment/status/' . $token);
        }
        Database::update('payments', ['status' => 'pending'], 'id = ?', [(int) $payment['id']]);
        $this->view('payment.bank', [
            'payment' => $payment,
            'reference' => PaymentService::bankReference($payment),
            'instructions' => SettingsService::paymentInstructions(),
            'amountEur' => (float) $payment['amount_eur'],
            'amountBgn' => (float) ($payment['amount_bgn'] ?? 0),
        ], 'layouts.app');
    }

    public function status(string $token): void
    {
        $payment = PaymentService::findByToken($token);
        if (!$payment) {
            http_response_code(404);
            echo 'Плащането не е намерено.';
            exit;
        }
        PaymentService::assertOwner($payment);
        $this->view('payment.status', [
            'payment' => $payment,
            'paid' => ($payment['status'] ?? '') === 'paid',
            'successUrl' => $this->successRedirectUrl($payment),
        ], 'layouts.app');
    }

    public function return(string $token): void
    {
        $payment = PaymentService::findByToken($token);
        if (!$payment) {
            Session::flash('error', 'Плащането не е намерено.');
            $this->redirect('/dashboard');
        }
        PaymentService::assertOwner($payment);
        $gatewaySlug = PaymentGatewayRegistry::resolveMethodSlug(
            (string) ($_GET['gateway'] ?? $payment['method'] ?? '')
        );
        $gw = PaymentGatewayRegistry::get($gatewaySlug);
        if ($gw !== null) {
            $gid = $gw->verifyReturn($payment, $_GET);
            if ($gid !== null) {
                PaymentService::handleGatewaySuccess($token, $gid, $gatewaySlug);
            }
        }
        $fresh = PaymentService::findByToken($token);
        if ($fresh && ($fresh['status'] ?? '') === 'paid') {
            Session::flash('success', 'Плащането е успешно. Заявената услуга е активирана.');
            $this->redirect($this->successRedirectUrl($fresh));
        }
        Session::flash('error', 'Плащането все още не е потвърдено. Опитайте отново или изчакайте няколко минути.');
        $this->redirect('/payment/status/' . $token);
    }

    public function cancel(string $token): void
    {
        $payment = PaymentService::findByToken($token);
        if ($payment) {
            PaymentService::assertOwner($payment);
            if (($payment['status'] ?? '') === 'pending') {
                Database::update('payments', ['status' => 'cancelled'], 'id = ?', [(int) $payment['id']]);
            }
        }
        Session::flash('error', 'Плащането е отменено.');
        $this->redirect('/dashboard');
    }

    public function redirectForm(string $token): void
    {
        $payment = PaymentService::findByToken($token);
        if (!$payment) {
            http_response_code(404);
            echo 'Плащането не е намерено.';
            exit;
        }
        PaymentService::assertOwner($payment);
        try {
            $checkout = PaymentService::initiateCheckout($payment);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/payment/status/' . $token);
        }
        if (!empty($checkout['html'])) {
            echo $checkout['html'];
            exit;
        }
        if (!empty($checkout['redirect'])) {
            $this->redirect($checkout['redirect']);
        }
        Session::flash('error', 'Неуспешно стартиране на плащане.');
        $this->redirect('/payment/status/' . $token);
    }

    /** @param array<string, mixed> $payment */
    private function successRedirectUrl(array $payment): string
    {
        return match ($payment['payable_type'] ?? '') {
            PaymentService::PAYABLE_SUBSCRIPTION => '/dashboard/subscription',
            PaymentService::PAYABLE_COMPETITION => '/dashboard/announcements/my',
            PaymentService::PAYABLE_EVENT => '/dashboard/events/my',
            default => '/dashboard',
        };
    }
}
