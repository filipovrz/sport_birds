<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\CheckoutFlowService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;

final class SubscriptionController extends Controller
{
    public function index(): void
    {
        $paymentInstructions = \App\Services\SettingsService::paymentInstructions();
        $user = Auth::user();
        $pending = Database::fetch(
            'SELECT sr.*, p.name AS plan_name FROM subscription_requests sr
             JOIN subscription_plans p ON p.id = sr.plan_id
             WHERE sr.user_id = ? AND sr.status = "pending" ORDER BY sr.id DESC LIMIT 1',
            [Auth::id()]
        );
        $this->view('subscription.index', [
            'plans' => SubscriptionService::plans(),
            'current' => SubscriptionService::currentPlan($user),
            'isPremium' => Auth::hasPremium(),
            'activePlanPrice' => SubscriptionService::activePaidPlanPrice($user),
            'paymentInstructions' => $paymentInstructions,
            'paymentMethods' => CheckoutFlowService::methodsForForms(),
            'pending' => $pending,
            'pendingPlanName' => $pending['plan_name'] ?? null,
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
        $user = Auth::user();
        $block = SubscriptionService::planRequestBlockReason($user, $plan);
        if ($block !== null) {
            Session::flash('error', $block);
            $this->redirect('/dashboard/subscription');
        }
        $requestId = Database::insert('subscription_requests', [
            'user_id' => Auth::id(),
            'plan_id' => $planId,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        $amount = (float) $plan['price_eur'];
        if ($amount <= 0) {
            Session::flash('success', 'Заявката е изпратена.');
            $this->redirect('/dashboard/subscription');
        }
        CheckoutFlowService::start(
            PaymentService::PAYABLE_SUBSCRIPTION,
            $requestId,
            $amount,
            'Абонамент: ' . ($plan['name'] ?? ''),
            CheckoutFlowService::paymentMethodFromPost()
        );
    }
}
