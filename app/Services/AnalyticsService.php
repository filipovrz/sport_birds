<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AnalyticsService
{
    /** @return array<string, mixed> */
    public static function userStats(int $userId): array
    {
        $stats = [
            'health_overdue' => 0,
            'training_30d' => 0,
            'competitions_ytd' => 0,
            'breeding_pairs' => 0,
            'recent_results' => [],
        ];
        try {
            $stats['health_overdue'] = (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM health_records WHERE user_id = ? AND next_due_at IS NOT NULL AND next_due_at < CURDATE()',
                [$userId]
            )['c'] ?? 0);
            $stats['training_30d'] = (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM training_sessions WHERE user_id = ? AND session_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)',
                [$userId]
            )['c'] ?? 0);
            $stats['competitions_ytd'] = (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM competitions WHERE user_id = ? AND YEAR(event_date) = YEAR(CURDATE())',
                [$userId]
            )['c'] ?? 0);
            $stats['breeding_pairs'] = (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM breeding_pairs WHERE user_id = ? AND season_year = YEAR(CURDATE())',
                [$userId]
            )['c'] ?? 0);
            $stats['recent_results'] = Database::fetchAll(
                'SELECT cr.*, c.name AS competition_name, b.ring_number
                 FROM competition_results cr
                 JOIN competitions c ON c.id = cr.competition_id
                 LEFT JOIN birds b ON b.id = cr.bird_id
                 WHERE c.user_id = ?
                 ORDER BY cr.id DESC
                 LIMIT 8',
                [$userId]
            );
        } catch (\Throwable) {
        }

        return $stats;
    }

    /** @return array<string, mixed> */
    public static function adminStats(): array
    {
        $stats = [
            'pending_ann_payments' => 0,
            'pending_event_payments' => 0,
            'paid_count_30d' => 0,
            'revenue_30d' => 0.0,
            'invoice_count' => 0,
            'proforma_count' => 0,
        ];
        try {
            $stats['pending_ann_payments'] = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM competition_announcements WHERE payment_status = 'pending'"
            )['c'] ?? 0);
            $stats['pending_event_payments'] = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM event_announcements WHERE payment_status = 'pending'"
            )['c'] ?? 0);
            $row = Database::fetch(
                "SELECT COUNT(*) AS c, COALESCE(SUM(amount_eur), 0) AS total
                 FROM payments
                 WHERE status = 'paid' AND paid_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
            );
            $stats['paid_count_30d'] = (int) ($row['c'] ?? 0);
            $stats['revenue_30d'] = (float) ($row['total'] ?? 0);
            $stats['invoice_count'] = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM invoices WHERE document_type = 'invoice'"
            )['c'] ?? 0);
            $stats['proforma_count'] = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM invoices WHERE document_type = 'proforma'"
            )['c'] ?? 0);
        } catch (\Throwable) {
        }

        return $stats;
    }
}
