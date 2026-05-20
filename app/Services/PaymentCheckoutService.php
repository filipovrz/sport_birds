<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Services\Payment\GatewayException;
use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayRegistry;

/**
 * Стартиране и възобновяване на плащания (от форми, футър, /payment-methods).
 */
final class PaymentCheckoutService
{
    /**
     * @param array{payable_type?: string, payable_id?: int, plan_id?: int, payment_token?: string} $context
     * @return never
     */
    public static function start(string $methodSlug, array $context = []): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Влезте в профила си, за да платите.');
            header('Location: /login?redirect=' . rawurlencode('/payment-methods/' . $methodSlug));
            exit;
        }

        $methodSlug = PaymentGatewayRegistry::resolveMethodSlug($methodSlug);

        if (!empty($context['payment_token'])) {
            self::resumePayment($context['payment_token'], $methodSlug);
        }

        if (!empty($context['plan_id'])) {
            self::startSubscription((int) $context['plan_id'], $methodSlug);
        }

        if (!empty($context['payable_type']) && !empty($context['payable_id'])) {
            self::startExistingPayable(
                (string) $context['payable_type'],
                (int) $context['payable_id'],
                $methodSlug
            );
        }

        $pending = self::findLatestPendingPayment((int) Auth::id(), $methodSlug);
        if ($pending !== null) {
            self::redirectToPayment($pending, $methodSlug);
        }

        Session::flash('error', 'Няма активна заявка за плащане. Изберете план или създайте обява.');
        header('Location: /payment-methods/' . rawurlencode($methodSlug));
        exit;
    }

    /** @return never */
    private static function startSubscription(int $planId, string $methodSlug): void
    {
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ? AND is_active = 1', [$planId]);
        if (!$plan || ($plan['slug'] ?? '') === 'free') {
            throw new GatewayException('Невалиден план.');
        }
        $user = Auth::user();
        $block = SubscriptionService::planRequestBlockReason($user, $plan);
        if ($block !== null) {
            Session::flash('error', $block);
            header('Location: /dashboard/subscription');
            exit;
        }

        $existing = Database::fetch(
            'SELECT sr.id FROM subscription_requests sr
             WHERE sr.user_id = ? AND sr.plan_id = ? AND sr.status = "pending" ORDER BY sr.id DESC LIMIT 1',
            [Auth::id(), $planId]
        );
        $requestId = $existing ? (int) $existing['id'] : Database::insert('subscription_requests', [
            'user_id' => Auth::id(),
            'plan_id' => $planId,
            'notes' => null,
        ]);

        $amount = (float) $plan['price_eur'];
        $payment = PaymentService::create(
            (int) Auth::id(),
            PaymentService::PAYABLE_SUBSCRIPTION,
            $requestId,
            $amount,
            $methodSlug,
            'Абонамент: ' . ($plan['name'] ?? '')
        );
        self::redirectToPayment($payment, $methodSlug);
    }

    /** @return never */
    private static function startExistingPayable(string $type, int $payableId, string $methodSlug): void
    {
        [$amount, $description] = self::resolvePayableAmount($type, $payableId);
        if ($amount <= 0) {
            Session::flash('error', 'Няма такса за плащане.');
            header('Location: /dashboard');
            exit;
        }
        $payment = PaymentService::findByPayable($type, $payableId);
        if ($payment === null || !in_array($payment['status'] ?? '', ['created', 'pending'], true)) {
            $payment = PaymentService::create(
                (int) Auth::id(),
                $type,
                $payableId,
                $amount,
                $methodSlug,
                $description
            );
        } elseif (($payment['method'] ?? '') !== $methodSlug) {
            PaymentService::updateMethod((int) $payment['id'], $methodSlug);
            $payment = PaymentService::findById((int) $payment['id']) ?? $payment;
        }
        self::redirectToPayment($payment, $methodSlug);
    }

    /** @return never */
    private static function resumePayment(string $token, string $methodSlug): void
    {
        $payment = PaymentService::findByToken($token);
        if ($payment === null || (int) $payment['user_id'] !== (int) Auth::id()) {
            Session::flash('error', 'Плащането не е намерено.');
            header('Location: /payment-methods');
            exit;
        }
        if (($payment['status'] ?? '') === 'paid') {
            Session::flash('success', 'Плащането вече е завършено.');
            header('Location: /payment/status/' . $token);
            exit;
        }
        if (($payment['method'] ?? '') !== $methodSlug) {
            PaymentService::updateMethod((int) $payment['id'], $methodSlug);
            $payment = PaymentService::findById((int) $payment['id']) ?? $payment;
        }
        self::redirectToPayment($payment, $methodSlug);
    }

    /** @param array<string, mixed> $payment */
    private static function redirectToPayment(array $payment, string $methodSlug): void
    {
        if ($methodSlug === 'bank') {
            header('Location: /payment/bank/' . $payment['public_token']);
            exit;
        }

        if (PaymentGatewayRegistry::get($methodSlug) === null) {
            $missing = PaymentConfig::missingFields($methodSlug);
            Session::flash(
                'error',
                'Методът „' . $methodSlug . '“ не е конфигуриран. Липсва: ' . implode(', ', $missing)
                . '. Попълнете в Админ → Настройки.'
            );
            header('Location: /payment/status/' . $payment['public_token']);
            exit;
        }

        header('Location: /payment/go/' . $payment['public_token']);
        exit;
    }

    /** @return array{0: float, 1: string} */
    private static function resolvePayableAmount(string $type, int $id): array
    {
        if ($type === PaymentService::PAYABLE_COMPETITION) {
            $row = Database::fetch('SELECT title, publish_fee_eur FROM competition_announcements WHERE id = ? AND user_id = ?', [$id, Auth::id()]);
            return [(float) ($row['publish_fee_eur'] ?? 0), 'Обява: ' . ($row['title'] ?? '')];
        }
        if ($type === PaymentService::PAYABLE_EVENT) {
            $row = Database::fetch('SELECT title, publish_fee_eur FROM event_announcements WHERE id = ? AND user_id = ?', [$id, Auth::id()]);
            return [(float) ($row['publish_fee_eur'] ?? 0), 'Събитие: ' . ($row['title'] ?? '')];
        }

        return [0.0, ''];
    }

    /** @return array<string, mixed>|null */
    public static function findLatestPendingPayment(int $userId, string $methodSlug): ?array
    {
        return Database::fetch(
            'SELECT * FROM payments WHERE user_id = ? AND method = ? AND status IN ("created","pending")
             ORDER BY id DESC LIMIT 1',
            [$userId, $methodSlug]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function pendingPaymentsForUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT * FROM payments WHERE user_id = ? AND status IN ("created","pending") ORDER BY id DESC LIMIT 10',
            [$userId]
        );
    }
}
