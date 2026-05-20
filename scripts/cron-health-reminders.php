<?php

declare(strict_types=1);

/**
 * Cron: здравни напомняния по имейл.
 * Пример: php scripts/cron-health-reminders.php
 * Crontab (дневно 08:00): 0 8 * * * cd /path/to/sport_birds && php scripts/cron-health-reminders.php
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap/app.php';

use App\Services\MailService;

$days = (int) ($argv[1] ?? 14);
$sent = 0;
$failed = 0;

foreach (MailService::healthRemindersDue($days) as $row) {
    $subject = 'Best Sport Byrds — напомняне за здравен преглед';
    $body = "Здравейте, {$row['user_name']}!\n\n";
    $body .= "Напомняне: {$row['title']}";
    if (!empty($row['ring_number'])) {
        $body .= " (птица {$row['ring_number']})";
    }
    $body .= "\nДата: {$row['next_due_at']}\n\n— Best Sport Byrds";
    if (MailService::send($row['email'], $subject, $body)) {
        $sent++;
    } else {
        $failed++;
    }
}

echo date('Y-m-d H:i:s') . " — изпратени: {$sent}, неуспешни: {$failed} (прозорец {$days} дни)\n";
