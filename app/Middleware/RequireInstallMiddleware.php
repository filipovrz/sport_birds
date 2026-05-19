<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\App;

final class RequireInstallMiddleware
{
    public function handle(?string $param = null): void
    {
        if (!App::isInstalled()) {
            header('Location: /install');
            exit;
        }
    }
}
