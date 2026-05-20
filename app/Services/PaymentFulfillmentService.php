<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\User;

final class PaymentFulfillmentService
{
  /**
   * Маркира плащането като платено и изпълнява заявената услуга.
   *
   * @param array<string, mixed> $payment
   */
    public static function markPaidAndFulfill(array $payment, ?string $gatewayPaymentId = null): bool
    {
        if (($payment['status'] ?? '') === 'paid') {
            return true;
        }
        Database::update('payments', [
            'status' => 'paid',
            'gateway_payment_id' => $gatewayPaymentId ?? $payment['gateway_payment_id'] ?? null,
            'paid_at' => date('Y-m-d H:i:s'),
        ], 'id = ? AND status != ?', [(int) $payment['id'], 'paid']);

        $fresh = PaymentService::findById((int) $payment['id']);
        if (!$fresh || ($fresh['status'] ?? '') !== 'paid') {
            return false;
        }

        return self::fulfillPayable($fresh);
    }

    /** @param array<string, mixed> $payment */
    public static function fulfillPayable(array $payment): bool
    {
        $type = $payment['payable_type'] ?? '';
        $id = (int) ($payment['payable_id'] ?? 0);

        return match ($type) {
            'subscription_request' => self::fulfillSubscription($id),
            'competition_announcement' => self::fulfillCompetitionAnnouncement($id),
            'event_announcement' => self::fulfillEventAnnouncement($id),
            default => false,
        };
    }

    private static function fulfillSubscription(int $requestId): bool
    {
        $req = Database::fetch('SELECT * FROM subscription_requests WHERE id = ?', [$requestId]);
        if (!$req || ($req['status'] ?? '') === 'approved') {
            return true;
        }
        if (($req['status'] ?? '') !== 'pending') {
            return false;
        }
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [$req['plan_id']]);
        if (!$plan) {
            return false;
        }
        $user = User::find((int) $req['user_id']);
        if ($user) {
            $block = SubscriptionService::planRequestBlockReason($user, $plan, false);
            if ($block !== null) {
                return false;
            }
        }
        $expires = date('Y-m-d H:i:s', strtotime('+' . (int) $plan['duration_days'] . ' days'));
        User::update((int) $req['user_id'], [
            'subscription_plan_id' => $plan['id'],
            'subscription_expires_at' => $expires,
        ]);
        Database::update('subscription_requests', [
            'status' => 'approved',
            'processed_by' => null,
            'processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$requestId]);

        return true;
    }

    private static function fulfillCompetitionAnnouncement(int $id): bool
    {
        $ann = Database::fetch('SELECT * FROM competition_announcements WHERE id = ?', [$id]);
        if (!$ann) {
            return false;
        }
        if (($ann['payment_status'] ?? '') === 'approved' && ($ann['status'] ?? '') === 'published') {
            return true;
        }
        Database::update('competition_announcements', [
            'status' => 'published',
            'payment_status' => 'approved',
            'payment_processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        return true;
    }

    private static function fulfillEventAnnouncement(int $id): bool
    {
        $ev = Database::fetch('SELECT * FROM event_announcements WHERE id = ?', [$id]);
        if (!$ev) {
            return false;
        }
        if (($ev['payment_status'] ?? '') === 'approved' && ($ev['status'] ?? '') === 'published') {
            return true;
        }
        Database::update('event_announcements', [
            'status' => 'published',
            'payment_status' => 'approved',
            'payment_processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        return true;
    }
}
