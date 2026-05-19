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
}
