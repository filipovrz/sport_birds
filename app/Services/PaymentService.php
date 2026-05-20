<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayRegistry;

final class PaymentService
{
    public const PAYABLE_SUBSCRIPTION = 'subscription_request';
    public const PAYABLE_COMPETITION = 'competition_announcement';
    public const PAYABLE_EVENT = 'event_announcement';

    /** @return array<string, mixed>|null */
    public static function findByToken(string $token): ?array
    {
        return Database::fetch('SELECT * FROM payments WHERE public_token = ?', [$token]);
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        return Database::fetch('SELECT * FROM payments WHERE id = ?', [$id]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function create(
        int $userId,
        string $payableType,
        int $payableId,
        float $amountEur,
        string $methodSlug,
        string $description
    ): array {
        $token = bin2hex(random_bytes(16));
        $idempotency = $payableType . ':' . $payableId . ':' . $methodSlug;
        $existing = Database::fetch(
            'SELECT * FROM payments WHERE idempotency_key = ? AND status IN ("created","pending","paid")',
            [$idempotency]
        );
        if ($existing) {
            return $existing;
        }
        $bgn = PaymentConfig::eurToBgn($amountEur);
        $id = Database::insert('payments', [
            'public_token' => $token,
            'user_id' => $userId,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
            'amount_eur' => number_format($amountEur, 2, '.', ''),
            'amount_bgn' => number_format($bgn, 2, '.', ''),
            'currency' => 'EUR',
            'method' => $methodSlug,
            'gateway' => $methodSlug,
            'status' => 'created',
            'idempotency_key' => $idempotency,
            'description' => mb_substr($description, 0, 255),
        ]);
        $payment = self::findById($id);
        if (!$payment) {
            throw new \RuntimeException('Плащането не е създадено.');
        }
        self::linkPayable($payableType, $payableId, $id, $methodSlug);

        if ($methodSlug === 'bank') {
            InvoiceService::issueProformaForPayment($id);
        }

        return $payment;
    }

    /**
     * @param array<string, mixed> $payment
     * @return array{redirect?: string, html?: string}
     */
    public static function initiateCheckout(array $payment): array
    {
        $slug = (string) ($payment['method'] ?? 'bank');
        $gateway = PaymentGatewayRegistry::get($slug);
        if ($gateway === null) {
            throw new \RuntimeException('Избраният метод за плащане не е наличен.');
        }
        $appUrl = rtrim((string) (require BASE_PATH . '/config/app.php')['url'], '/');
        $token = $payment['public_token'];
        $returnUrl = $appUrl . '/payment/return/' . $token;
        $cancelUrl = $appUrl . '/payment/cancel/' . $token;
        $result = $gateway->startCheckout($payment, $returnUrl, $cancelUrl);
        Database::update('payments', [
            'status' => 'pending',
            'gateway_session_id' => $result['session_id'] ?? null,
        ], 'id = ?', [(int) $payment['id']]);
        if (!empty($result['html'])) {
            return ['html' => $result['html']];
        }
        if (!empty($result['redirect_url'])) {
            return ['redirect' => $result['redirect_url']];
        }

        throw new \RuntimeException('Няма URL за плащане.');
    }

    /** @param array<string, mixed> $payment */
    public static function bankReference(array $payment): string
    {
        return 'BSB-' . str_pad((string) $payment['id'], 8, '0', STR_PAD_LEFT);
    }

    /** Маркира плащането като платено и издава фактура (идемпотентно). */
    public static function markPaid(int $paymentId, ?string $gatewayPaymentId = null): bool
    {
        $payment = self::findById($paymentId);
        if (!$payment) {
            return false;
        }
        if (($payment['status'] ?? '') !== 'paid') {
            Database::update('payments', [
                'status' => 'paid',
                'gateway_payment_id' => $gatewayPaymentId ?? $payment['gateway_payment_id'] ?? null,
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = ? AND status != ?', [$paymentId, 'paid']);
        }
        InvoiceService::issueForPayment($paymentId);

        $fresh = self::findById($paymentId);

        return $fresh !== null && ($fresh['status'] ?? '') === 'paid';
    }

    /** @return array<string, mixed>|null */
    public static function findByPayable(string $type, int $payableId): ?array
    {
        return Database::fetch(
            'SELECT * FROM payments WHERE payable_type = ? AND payable_id = ? ORDER BY id DESC LIMIT 1',
            [$type, $payableId]
        );
    }

    public static function updateMethod(int $paymentId, string $methodSlug): void
    {
        Database::update('payments', [
            'method' => $methodSlug,
            'gateway' => $methodSlug,
            'status' => 'created',
            'gateway_session_id' => null,
            'gateway_payment_id' => null,
        ], 'id = ?', [$paymentId]);
    }

    /** Обработка на return URL след плащане при доставчик. */
    public static function processReturn(array $payment, array $query, array $post = []): bool
    {
        $gatewaySlug = PaymentGatewayRegistry::resolveMethodSlug(
            (string) ($query['gateway'] ?? $payment['method'] ?? '')
        );
        $gw = PaymentGatewayRegistry::get($gatewaySlug);
        if ($gw === null) {
            return false;
        }
        $merged = array_merge($query, $post);
        $gid = $gw->verifyReturn($payment, $merged);
        if ($gid === null && $gatewaySlug === 'stripe') {
            $sessionId = trim((string) ($payment['gateway_session_id'] ?? ''));
            if ($sessionId !== '') {
                $gid = $gw->verifyReturn($payment, ['session_id' => $sessionId]);
            }
        }
        if ($gid === null) {
            return false;
        }

        return self::handleGatewaySuccess((string) $payment['public_token'], $gid, $gatewaySlug);
    }

    public static function handleGatewaySuccess(string $token, ?string $gatewayPaymentId, ?string $gateway = null): bool
    {
        $payment = self::findByToken($token);
        if (!$payment) {
            $payment = self::findById((int) $token);
        }
        if (!$payment) {
            return false;
        }

        return PaymentFulfillmentService::markPaidAndFulfill($payment, $gatewayPaymentId);
    }

    public static function handleWebhook(string $gatewaySlug, string $rawBody, array $headers): bool
    {
        $gw = PaymentGatewayRegistry::get($gatewaySlug);
        if ($gw === null) {
            return false;
        }
        $parsed = $gw->verifyWebhook($rawBody, $headers);
        if ($parsed === null) {
            return false;
        }
        $payment = null;
        if (!empty($parsed['payment_token'])) {
            $payment = self::findByToken((string) $parsed['payment_token']);
        }
        if ($payment === null && !empty($parsed['payment_id'])) {
            $payment = self::findById((int) $parsed['payment_id']);
        }
        if ($payment === null) {
            return false;
        }

        return PaymentFulfillmentService::markPaidAndFulfill(
            $payment,
            (string) ($parsed['gateway_payment_id'] ?? '')
        );
    }

    /** @param array<string, mixed> $payment */
    public static function assertOwner(array $payment): void
    {
        if (!Auth::check() || (int) Auth::id() !== (int) $payment['user_id']) {
            http_response_code(403);
            echo 'Нямате достъп до това плащане.';
            exit;
        }
    }

    private static function linkPayable(string $type, int $payableId, int $paymentId, string $method): void
    {
        $ref = self::bankReference(self::findById($paymentId) ?? ['id' => $paymentId]);
        match ($type) {
            self::PAYABLE_SUBSCRIPTION => Database::update('subscription_requests', [
                'payment_id' => $paymentId,
                'payment_method' => $method,
                'payment_reference' => $ref,
            ], 'id = ?', [$payableId]),
            self::PAYABLE_COMPETITION => Database::update('competition_announcements', [
                'payment_id' => $paymentId,
                'payment_reference' => $ref,
            ], 'id = ?', [$payableId]),
            self::PAYABLE_EVENT => Database::update('event_announcements', [
                'payment_id' => $paymentId,
                'payment_reference' => $ref,
            ], 'id = ?', [$payableId]),
            default => null,
        };
    }
}
