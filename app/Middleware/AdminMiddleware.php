<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Services\AdminPermissionService;

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
        if (Auth::isSuperAdmin()) {
            return;
        }
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin';
        $required = AdminPermissionService::permissionForAdminPath($path);
        if ($required !== null && !AdminPermissionService::can($required)) {
            http_response_code(403);
            echo 'Нямате право за тази секция. Свържете се със супер администратор.';
            exit;
        }
    }
}
