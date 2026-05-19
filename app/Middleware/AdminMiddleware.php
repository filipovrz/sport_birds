<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

final class AdminMiddleware
{
    public function handle(?string $param = null): void
    {
        if ($param === 'super_admin') {
            if (!Auth::isSuperAdmin()) {
                http_response_code(403);
                echo 'Достъпът е отказан.';
                exit;
            }
            return;
        }
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo 'Достъпът е отказан.';
            exit;
        }
    }
}
