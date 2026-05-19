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
        $plans = Database::fetchAll('SELECT * FROM subscription_plans ORDER BY sort_order');
        $this->view('admin.plans.index', ['plans' => $plans], 'layouts.admin');
    }

    public function create(): void
    {
        $this->view('admin.plans.form', ['plan' => null], 'layouts.admin');
    }

    public function store(): void
    {
        $d = $this->validate(['name' => 'required', 'slug' => 'required']);
        Database::insert('subscription_plans', [
            'name' => $d['name'],
            'slug' => $d['slug'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'price_bgn' => (float) ($_POST['price_bgn'] ?? 0),
            'duration_days' => (int) ($_POST['duration_days'] ?? 30),
            'max_birds' => ($_POST['max_birds'] ?? '') !== '' ? (int) $_POST['max_birds'] : null,
            'max_lofts' => ($_POST['max_lofts'] ?? '') !== '' ? (int) $_POST['max_lofts'] : null,
            'features' => json_encode(array_filter(explode(',', $_POST['features'] ?? ''))),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        Session::flash('success', 'Планът е създаден.');
        $this->redirect('/admin/plans');
    }

    public function edit(string $id): void
    {
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [(int) $id]);
        $this->view('admin.plans.form', ['plan' => $plan], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $d = $this->validate(['name' => 'required']);
        Database::update('subscription_plans', [
            'name' => $d['name'],
            'price_bgn' => (float) ($_POST['price_bgn'] ?? 0),
            'duration_days' => (int) ($_POST['duration_days'] ?? 30),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Планът е обновен.');
        $this->redirect('/admin/plans');
    }
}
