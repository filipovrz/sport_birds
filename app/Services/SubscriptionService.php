<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Bird;
use App\Models\Loft;

final class SubscriptionService
{
    public static function canAddBird(int $userId): bool
    {
        $user = Auth::user();
        if (!$user || $user['id'] !== $userId) {
            return false;
        }
        if (Auth::hasPremium()) {
            $plan = self::currentPlan($user);
            if (!$plan || $plan['max_birds'] === null) {
                return true;
            }
            return Bird::countForUser($userId) < (int) $plan['max_birds'];
        }
        $config = require BASE_PATH . '/config/app.php';
        return Bird::countForUser($userId) < $config['free_bird_limit'];
    }

    public static function canAddLoft(int $userId): bool
    {
        $user = Auth::user();
        if (Auth::hasPremium()) {
            $plan = self::currentPlan($user);
            if (!$plan || $plan['max_lofts'] === null) {
                return true;
            }
            return Loft::countForUser($userId) < (int) $plan['max_lofts'];
        }
        $config = require BASE_PATH . '/config/app.php';
        return Loft::countForUser($userId) < $config['free_loft_limit'];
    }

    public static function hasFeature(string $feature): bool
    {
        if (!Auth::hasPremium()) {
            return in_array($feature, ['birds', 'lofts', 'basic_health'], true);
        }
        $plan = self::currentPlan(Auth::user());
        if (!$plan) {
            return true;
        }
        $features = json_decode($plan['features'] ?? '[]', true) ?: [];
        return in_array('all', $features, true) || in_array($feature, $features, true);
    }

    /** @return array|null */
    public static function currentPlan(?array $user): ?array
    {
        if (!$user || !$user['subscription_plan_id']) {
            return Database::fetch("SELECT * FROM subscription_plans WHERE slug = 'free'");
        }
        return Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [$user['subscription_plan_id']]);
    }

    public static function plans(): array
    {
        return Database::fetchAll('SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY sort_order');
    }

    public static function hasPendingRequest(int $userId): bool
    {
        return (bool) Database::fetch(
            'SELECT id FROM subscription_requests WHERE user_id = ? AND status = ? LIMIT 1',
            [$userId, 'pending']
        );
    }

    /** Активен платен абонамент (не безплатен, не изтекъл). */
    public static function activePaidPlanPrice(array $user): float
    {
        if (!self::userHasActivePremium($user)) {
            return 0.0;
        }
        $plan = self::currentPlan($user);
        if (!$plan || ($plan['slug'] ?? '') === 'free') {
            return 0.0;
        }

        return (float) ($plan['price_eur'] ?? 0);
    }

    /** @return string|null Съобщение за грешка или null ако заявката е позволена. */
    public static function planRequestBlockReason(array $user, array $targetPlan, bool $checkPending = true): ?string
    {
        if (($targetPlan['slug'] ?? '') === 'free') {
            return 'Невалиден план.';
        }
        if ($checkPending && self::hasPendingRequest((int) $user['id'])) {
            return 'Вече имате заявка, която чака одобрение.';
        }
        $currentPrice = self::activePaidPlanPrice($user);
        $targetPrice = (float) ($targetPlan['price_eur'] ?? 0);
        if ($currentPrice > 0 && $targetPrice <= $currentPrice) {
            return 'Можете да заявите само по-скъп план от текущия (' . format_eur($currentPrice) . ').';
        }

        return null;
    }

    public static function userHasActivePremium(array $user): bool
    {
        if (in_array($user['role'] ?? '', ['admin', 'super_admin'], true)) {
            return true;
        }
        if (empty($user['subscription_plan_id'])) {
            return false;
        }
        if (empty($user['subscription_expires_at'])) {
            return false;
        }

        return strtotime($user['subscription_expires_at']) >= time();
    }
}
