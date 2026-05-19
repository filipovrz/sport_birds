<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

final class PremiumMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Auth::hasPremium()) {
            Session::flash('error', 'Тази функция изисква платен абонамент.');
            header('Location: /dashboard/subscription');
            exit;
        }
    }
}
