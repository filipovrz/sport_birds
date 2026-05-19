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
        ];
        $this->view('dashboard.index', [
            'stats' => $stats,
            'plan' => SubscriptionService::currentPlan(Auth::user()),
            'isPremium' => Auth::hasPremium(),
        ]);
    }
}
