<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class MailService
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $from = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'contact_email'");
        $fromEmail = $from['value'] ?? 'noreply@localhost';
        $headers = "From: {$fromEmail}\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        return @mail($to, $subject, $body, $headers);
    }

    /** @return list<array> */
    public static function healthRemindersDue(int $daysAhead = 7): array
    {
        return Database::fetchAll(
            'SELECT h.*, u.email, u.name AS user_name, b.ring_number
             FROM health_records h
             JOIN users u ON u.id = h.user_id
             LEFT JOIN birds b ON b.id = h.bird_id
             WHERE h.next_due_at IS NOT NULL
               AND h.next_due_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY h.next_due_at',
            [$daysAhead]
        );
    }
}
