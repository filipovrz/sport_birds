<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class SystemController extends Controller
{
    public function index(): void
    {
        $this->view('super_admin.system', [
            'php' => PHP_VERSION,
            'env' => $_ENV['APP_ENV'] ?? 'unknown',
        ], 'layouts.admin');
    }

    public function update(): void
    {
        if (isset($_POST['maintenance_mode'])) {
            Database::query(
                'INSERT INTO settings (`key`, `value`) VALUES ("maintenance_mode", ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$_POST['maintenance_mode'] === '1' ? '1' : '0']
            );
        }
        Session::flash('success', 'Системните настройки са обновени.');
        $this->redirect('/super-admin/system');
    }
}
