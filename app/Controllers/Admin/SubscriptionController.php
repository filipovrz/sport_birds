<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\User;

final class SubscriptionController extends Controller
{
    public function index(): void
    {
        $requests = Database::fetchAll(
            'SELECT sr.*, u.name AS user_name, u.email, p.name AS plan_name
             FROM subscription_requests sr
             JOIN users u ON u.id = sr.user_id
             JOIN subscription_plans p ON p.id = sr.plan_id
             ORDER BY sr.created_at DESC'
        );
        $this->view('admin.subscriptions.index', ['requests' => $requests], 'layouts.admin');
    }

    public function approve(string $id): void
    {
        $req = Database::fetch('SELECT * FROM subscription_requests WHERE id = ?', [(int) $id]);
        if (!$req) {
            $this->redirect('/admin/subscriptions');
        }
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [$req['plan_id']]);
        $expires = date('Y-m-d H:i:s', strtotime('+' . $plan['duration_days'] . ' days'));
        User::update((int) $req['user_id'], [
            'subscription_plan_id' => $plan['id'],
            'subscription_expires_at' => $expires,
        ]);
        Database::update('subscription_requests', [
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Абонаментът е активиран.');
        $this->redirect('/admin/subscriptions');
    }

    public function reject(string $id): void
    {
        Database::update('subscription_requests', [
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => date('Y-m-d H:i:s'),
            'admin_notes' => trim($_POST['admin_notes'] ?? '') ?: null,
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Заявката е отхвърлена.');
        $this->redirect('/admin/subscriptions');
    }
}
