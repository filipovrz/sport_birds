<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\LocaleService;

final class LocaleController extends Controller
{
    public function switch(string $lang): void
    {
        LocaleService::set($lang);
        $ref = trim((string) ($_GET['return'] ?? ''));
        if ($ref === '' || !str_starts_with($ref, '/')) {
            $ref = Auth::check() ? '/dashboard' : '/';
        }
        $this->redirect($ref);
    }
}
