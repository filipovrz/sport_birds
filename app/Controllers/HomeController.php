<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SubscriptionService;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home.index', [
            'plans' => SubscriptionService::plans(),
        ], 'layouts.guest');
    }

    public function pricing(): void
    {
        $this->view('home.pricing', [
            'plans' => SubscriptionService::plans(),
        ], 'layouts.guest');
    }
}
