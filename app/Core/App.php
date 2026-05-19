<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    public static function isInstalled(): bool
    {
        if (!is_file(BASE_PATH . '/.env')) {
            return false;
        }
        try {
            $row = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'app_installed'");
            return $row && $row['value'] === '1';
        } catch (\Throwable) {
            return false;
        }
    }

    public static function notFound(): never
    {
        http_response_code(404);
        echo View::render('errors.404', [], 'layouts.guest');
        exit;
    }
}
