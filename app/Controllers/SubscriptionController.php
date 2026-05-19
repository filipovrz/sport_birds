<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\SubscriptionService;

final class SubscriptionController extends Controller
{
    public function index(): void
    {
        $settings = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'payment_instructions'");
        $this->view('subscription.index', [
            'plans' => SubscriptionService::plans(),
            'current' => SubscriptionService::currentPlan(Auth::user()),
            'isPremium' => Auth::hasPremium(),
            'paymentInstructions' => $settings['value'] ?? '',
            'pending' => Database::fetch(
                'SELECT * FROM subscription_requests WHERE user_id = ? AND status = "pending" ORDER BY id DESC LIMIT 1',
                [Auth::id()]
            ),
        ]);
    }

    public function requestPlan(): void
    {
        $planId = (int) ($_POST['plan_id'] ?? 0);
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ? AND is_active = 1', [$planId]);
        if (!$plan || $plan['slug'] === 'free') {
            Session::flash('error', 'Невалиден план.');
            $this->back();
        }
        Database::insert('subscription_requests', [
            'user_id' => Auth::id(),
            'plan_id' => $planId,
            'payment_reference' => trim($_POST['payment_reference'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Заявката е изпратена. Администраторът ще я активира след потвърждение на плащането.');
        $this->redirect('/dashboard/subscription');
    }
}
