<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Services\EmailVerificationService;

final class GuestMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Auth::check()) {
            return;
        }
        $user = Auth::user();
        if ($user && EmailVerificationService::needsVerification($user)) {
            header('Location: /verify-email/pending?email=' . urlencode($user['email'] ?? ''));
            exit;
        }
        header('Location: /dashboard');
        exit;
    }
}
