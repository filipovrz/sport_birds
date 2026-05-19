<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class PlanController extends Controller
{
    public function index(): void
    {
        $plans = Database::fetchAll('SELECT * FROM subscription_plans ORDER BY sort_order, id');
        $this->view('admin.plans.index', ['plans' => $plans], 'layouts.admin');
    }

    public function create(): void
    {
        $this->view('admin.plans.form', ['plan' => null], 'layouts.admin');
    }

    public function store(): void
    {
        $d = $this->validate(['name' => 'required', 'slug' => 'required']);
        Database::insert('subscription_plans', $this->planPayload($d, true));
        Session::flash('success', 'Планът е създаден.');
        $this->redirect('/admin/plans');
    }

    public function edit(string $id): void
    {
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [(int) $id]);
        if (!$plan) {
            Session::flash('error', 'Планът не е намерен.');
            $this->redirect('/admin/plans');
        }
        $this->view('admin.plans.form', ['plan' => $plan], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $d = $this->validate(['name' => 'required']);
        $planId = (int) $id;
        $existing = Database::fetch('SELECT id FROM subscription_plans WHERE id = ?', [$planId]);
        if (!$existing) {
            Session::flash('error', 'Планът не е намерен.');
            $this->redirect('/admin/plans');
        }
        $payload = $this->planPayload($d, false);
        if (trim($_POST['slug'] ?? '') !== '') {
            $payload['slug'] = trim($_POST['slug']);
        }
        Database::update('subscription_plans', $payload, 'id = ?', [$planId]);
        Session::flash('success', 'Планът е обновен.');
        $this->redirect('/admin/plans');
    }

    public function destroy(string $id): void
    {
        $planId = (int) $id;
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [$planId]);
        if (!$plan) {
            Session::flash('error', 'Планът не е намерен.');
            $this->redirect('/admin/plans');
        }
        if ($plan['slug'] === 'free') {
            Session::flash('error', 'Безплатният план не може да се изтрива.');
            $this->redirect('/admin/plans');
        }
        $users = Database::fetch('SELECT COUNT(*) AS c FROM users WHERE subscription_plan_id = ?', [$planId]);
        if ((int) ($users['c'] ?? 0) > 0) {
            Session::flash('error', 'Планът се използва от потребители. Деактивирайте го вместо изтриване.');
            $this->redirect('/admin/plans');
        }
        $requests = Database::fetch('SELECT COUNT(*) AS c FROM subscription_requests WHERE plan_id = ?', [$planId]);
        if ((int) ($requests['c'] ?? 0) > 0) {
            Session::flash('error', 'Има заявки за този план. Първо ги обработете или деактивирайте плана.');
            $this->redirect('/admin/plans');
        }
        Database::query('DELETE FROM subscription_plans WHERE id = ?', [$planId]);
        Session::flash('success', 'Планът е изтрит.');
        $this->redirect('/admin/plans');
    }

    /** @param array<string, string> $d */
    /** @return array<string, mixed> */
    private function planPayload(array $d, bool $includeSlug): array
    {
        $featuresRaw = trim($_POST['features'] ?? '');
        $features = $featuresRaw !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $featuresRaw))))
            : [];

        $payload = [
            'name' => $d['name'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'price_eur' => (float) ($_POST['price_eur'] ?? 0),
            'duration_days' => (int) ($_POST['duration_days'] ?? 30),
            'max_birds' => ($_POST['max_birds'] ?? '') !== '' ? (int) $_POST['max_birds'] : null,
            'max_lofts' => ($_POST['max_lofts'] ?? '') !== '' ? (int) $_POST['max_lofts'] : null,
            'features' => json_encode($features, JSON_UNESCAPED_UNICODE),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($includeSlug) {
            $payload['slug'] = $d['slug'] ?? trim($_POST['slug'] ?? '');
        }

        return $payload;
    }
}
