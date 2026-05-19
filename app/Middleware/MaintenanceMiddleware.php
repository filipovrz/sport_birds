<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Database;

final class MaintenanceMiddleware
{
    public function handle(?string $param = null): void
    {
        try {
            $row = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'maintenance_mode'");
            if (!$row || $row['value'] !== '1') {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        if (Auth::isAdmin()) {
            return;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (str_starts_with($uri, '/login') || str_starts_with($uri, '/install')) {
            return;
        }

        http_response_code(503);
        echo '<!DOCTYPE html><html lang="bg"><head><meta charset="UTF-8"><title>Поддръжка</title></head>';
        echo '<body style="font-family:sans-serif;text-align:center;padding:3rem"><h1>Сайтът е в режим поддръжка</h1>';
        echo '<p>Моля, опитайте по-късно.</p></body></html>';
        exit;
    }
}
