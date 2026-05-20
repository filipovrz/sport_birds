<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\PaymentMethodsService;

final class SettingsController extends Controller
{
    public function index(): void
    {
        $rows = Database::fetchAll('SELECT * FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.settings', ['settings' => $settings, 'config' => $config], 'layouts.admin');
    }

    public function update(): void
    {
        foreach (['site_name', 'contact_email'] as $key) {
            if (isset($_POST[$key])) {
                Database::query(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$key, $_POST[$key]]
                );
            }
        }
        if (isset($_POST['payment_instructions'])) {
            $bank = trim((string) $_POST['payment_instructions']);
            if ($bank !== '' && PaymentMethodsService::looksLikeAccidentalPaste($bank)) {
                Session::flash('error', 'Банковите реквизити не са запазени — полето съдържаше описание на методи, не IBAN/сметка. Редактирайте методи във „Футър“.');
            } elseif ($bank !== '') {
                Database::query(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    ['payment_instructions', $bank]
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
        $gatewayKeys = [
            'payment_eur_bgn_rate', 'stripe_secret_key', 'stripe_webhook_secret',
            'epay_min', 'epay_secret', 'epay_url',
            'paypal_client_id', 'paypal_secret', 'paypal_mode',
            'revolut_api_secret', 'revolut_mode',
        ];
        foreach ($gatewayKeys as $key) {
            if (array_key_exists($key, $_POST)) {
                Database::query(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$key, trim((string) $_POST[$key])]
                );
            }
        }
        Session::flash('success', 'Настройките са запазени.');
        $this->redirect('/admin/settings');
    }
}
