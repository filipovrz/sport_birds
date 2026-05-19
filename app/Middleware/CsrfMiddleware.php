<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Session;

final class CsrfMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!Csrf::verify()) {
            Session::flash('error', 'Невалидна сесия. Опитайте отново.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }
    }
}
