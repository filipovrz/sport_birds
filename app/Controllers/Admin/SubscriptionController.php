<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SettingsService;
use App\Services\SubscriptionService;

final class SubscriptionController extends Controller
{
    public function index(): void
    {
        $requests = Database::fetchAll(
            'SELECT sr.*, u.name AS user_name, p.name AS plan_name, p.price_eur
             FROM subscription_requests sr
             JOIN users u ON u.id = sr.user_id
             JOIN subscription_plans p ON p.id = sr.plan_id
             ORDER BY sr.created_at DESC'
        );
        $this->view('admin.subscriptions.index', ['requests' => $requests], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $req = $this->findRequest((int) $id);
        if (!$req) {
            Session::flash('error', 'Заявката не е намерена.');
            $this->redirect('/admin/subscriptions');
        }
        $this->view('admin.subscriptions.show', ['req' => $req], 'layouts.admin');
    }

    public function print(string $id): void
    {
        $req = $this->findRequest((int) $id);
        if (!$req) {
            http_response_code(404);
            echo 'Заявката не е намерена.';
            exit;
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.subscriptions.print', [
            'req' => $req,
            'config' => $config,
            'paymentInstructions' => SettingsService::paymentInstructions(),
        ], null);
    }

    public function approve(string $id): void
    {
        $req = Database::fetch('SELECT * FROM subscription_requests WHERE id = ?', [(int) $id]);
        if (!$req) {
            $this->redirect('/admin/subscriptions');
        }
        if (($req['status'] ?? '') !== 'pending') {
            Session::flash('error', 'Заявката вече е обработена.');
            $this->redirect('/admin/subscriptions/' . $id);
        }
        $plan = Database::fetch('SELECT * FROM subscription_plans WHERE id = ?', [$req['plan_id']]);
        $user = User::find((int) $req['user_id']);
        if ($user) {
            $block = SubscriptionService::planRequestBlockReason($user, $plan, false);
            if ($block !== null) {
                Session::flash('error', $block);
                $this->redirect('/admin/subscriptions/' . $id);
            }
        }
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
        if (!empty($req['payment_id'])) {
            $pay = PaymentService::findById((int) $req['payment_id']);
            if ($pay && ($pay['status'] ?? '') !== 'paid') {
                Database::update('payments', [
                    'status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                ], 'id = ?', [(int) $req['payment_id']]);
            }
        }
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

    public function destroy(string $id): void
    {
        if (!$this->findRequest((int) $id)) {
            Session::flash('error', 'Заявката не е намерена.');
            $this->redirect('/admin/subscriptions');
        }
        Database::query('DELETE FROM subscription_requests WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Заявката е изтрита.');
        $this->redirect('/admin/subscriptions');
    }

    /** @return array|null */
    private function findRequest(int $id): ?array
    {
        return Database::fetch(
            'SELECT sr.*, u.name AS user_name, u.email, u.phone, u.city,
                    p.name AS plan_name, p.slug AS plan_slug, p.price_eur, p.duration_days,
                    p.description AS plan_description,
                    pb.name AS processed_by_name
             FROM subscription_requests sr
             JOIN users u ON u.id = sr.user_id
             JOIN subscription_plans p ON p.id = sr.plan_id
             LEFT JOIN users pb ON pb.id = sr.processed_by
             WHERE sr.id = ?',
            [$id]
        );
    }
}
