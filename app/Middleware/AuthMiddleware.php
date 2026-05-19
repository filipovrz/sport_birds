<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Services\EmailVerificationService;

final class AuthMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        $user = Auth::user();
        if (!$user || !EmailVerificationService::needsVerification($user)) {
            return;
        }
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $allowed = ['/verify-email/pending', '/verify-email/resend', '/logout'];
        foreach ($allowed as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix)) {
                return;
            }
        }
        if ($path === '/verify-email' && isset($_GET['token'])) {
            return;
        }
        header('Location: /verify-email/pending?email=' . urlencode($user['email'] ?? ''));
        exit;
    }
}
