<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Bird;
use App\Models\Loft;
use App\Services\SubscriptionService;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $uid = Auth::id();
        $stats = [
            'birds' => Bird::countForUser($uid),
            'lofts' => Loft::countForUser($uid),
            'upcoming_health' => Database::fetchAll(
                'SELECT * FROM health_records WHERE user_id = ? AND next_due_at IS NOT NULL AND next_due_at >= CURDATE()
                 ORDER BY next_due_at LIMIT 5',
                [$uid]
            ),
            'recent_competitions' => Database::fetchAll(
                'SELECT * FROM competitions WHERE user_id = ? ORDER BY event_date DESC LIMIT 5',
                [$uid]
            ),
            'gps_active' => 0,
            'announcements_open' => 0,
        ];
        try {
            $stats['gps_active'] = (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM gps_devices WHERE user_id = ? AND is_active = 1',
                [$uid]
            )['c'] ?? 0);
            $stats['announcements_open'] = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM competition_announcements WHERE status = 'published' AND event_date >= CURDATE()"
            )['c'] ?? 0);
        } catch (\Throwable) {
        }
        $cfg = require BASE_PATH . '/config/app.php';
        $this->view('dashboard.index', [
            'stats' => $stats,
            'plan' => SubscriptionService::currentPlan(Auth::user()),
            'isPremium' => Auth::hasPremium(),
            'appVersion' => $cfg['version'] ?? '2.0.0',
        ]);
    }
}
