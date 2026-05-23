<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\AnalyticsService;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            'users' => Database::fetch('SELECT COUNT(*) AS c FROM users')['c'] ?? 0,
            'birds' => Database::fetch('SELECT COUNT(*) AS c FROM birds')['c'] ?? 0,
            'pending_subs' => Database::fetch('SELECT COUNT(*) AS c FROM subscription_requests WHERE status = "pending"')['c'] ?? 0,
        ];
        $this->view('admin.dashboard', [
            'stats' => $stats,
            'analytics' => AnalyticsService::adminStats(),
        ], 'layouts.admin');
    }
}
