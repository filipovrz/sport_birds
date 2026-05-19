<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('super_admin.dashboard', [], 'layouts.admin');
    }
}
