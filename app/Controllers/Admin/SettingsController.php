<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class SettingsController extends Controller
{
    public function index(): void
    {
        $rows = Database::fetchAll('SELECT * FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        $this->view('admin.settings', ['settings' => $settings], 'layouts.admin');
    }

    public function update(): void
    {
        foreach (['site_name', 'contact_email', 'payment_instructions'] as $key) {
            if (isset($_POST[$key])) {
                Database::query(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$key, $_POST[$key]]
                );
            }
        }
        if (isset($_POST['announcement_publish_fee_eur'])) {
            $fee = max(0, (float) $_POST['announcement_publish_fee_eur']);
            Database::query(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                ['announcement_publish_fee_eur', number_format($fee, 2, '.', '')]
            );
        }
        if (isset($_POST['event_publish_fee_eur'])) {
            $fee = max(0, (float) $_POST['event_publish_fee_eur']);
            Database::query(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                ['event_publish_fee_eur', number_format($fee, 2, '.', '')]
            );
        }
        Session::flash('success', 'Настройките са запазени.');
        $this->redirect('/admin/settings');
    }
}
