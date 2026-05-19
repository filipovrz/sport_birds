<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;

final class InstallLockMiddleware
{
    public function handle(?string $param = null): void
    {
        try {
            $row = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'app_installed'");
            if ($row && $row['value'] === '1') {
                http_response_code(403);
                echo 'Приложението вече е инсталирано. <a href="/login">Вход</a>';
                exit;
            }
        } catch (\Throwable) {
            // allow install if DB not ready
        }
    }
}
