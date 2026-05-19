<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Services\MailService;

final class NotificationController extends Controller
{
    public function sendHealthReminders(): void
    {
        $sent = 0;
        foreach (MailService::healthRemindersDue(14) as $row) {
            $subject = 'Best Sport Byrds — напомняне за здравен преглед';
            $body = "Здравейте, {$row['user_name']}!\n\n";
            $body .= "Напомняне: {$row['title']}";
            if ($row['ring_number']) {
                $body .= " (птица {$row['ring_number']})";
            }
            $body .= "\nДата: {$row['next_due_at']}\n\n— Best Sport Byrds";
            if (MailService::send($row['email'], $subject, $body)) {
                $sent++;
            }
        }
        Session::flash('success', "Изпратени са {$sent} имейла (ако сървърът поддържа mail()).");
        $this->redirect('/admin');
    }
}
