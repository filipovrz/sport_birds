<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\PaymentService;
use App\Services\SettingsService;

final class AnnouncementPaymentController extends Controller
{
    public function index(): void
    {
        $pending = Database::fetchAll(
            "SELECT a.*, u.name AS user_name, u.email
             FROM competition_announcements a
             JOIN users u ON u.id = a.user_id
             WHERE a.payment_status = 'pending'
             ORDER BY a.created_at ASC"
        );
        $history = Database::fetchAll(
            "SELECT a.*, u.name AS user_name, u.email
             FROM competition_announcements a
             JOIN users u ON u.id = a.user_id
             WHERE a.payment_status IN ('approved', 'rejected')
             ORDER BY COALESCE(a.payment_processed_at, a.updated_at) DESC
             LIMIT 200"
        );
        $this->view('admin.announcement_payments.index', [
            'announcements' => $pending,
            'history' => $history,
            'publishFee' => SettingsService::announcementPublishFeeEur(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $ann = $this->findPayment((int) $id);
        if (!$ann) {
            Session::flash('error', 'Плащането не е намерено.');
            $this->redirect('/admin/announcement-payments');
        }
        $this->view('admin.announcement_payments.show', ['ann' => $ann], 'layouts.admin');
    }

    public function print(string $id): void
    {
        $ann = $this->findPayment((int) $id);
        if (!$ann) {
            http_response_code(404);
            echo 'Плащането не е намерено.';
            exit;
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.announcement_payments.print', [
            'ann' => $ann,
            'config' => $config,
            'paymentInstructions' => SettingsService::paymentInstructions(),
        ], null);
    }

    public function approve(string $id): void
    {
        $ann = $this->findPending((int) $id);
        if (!$ann) {
            $this->redirect('/admin/announcement-payments');
        }
        Database::update('competition_announcements', [
            'status' => 'published',
            'payment_status' => 'approved',
            'payment_processed_by' => Auth::id(),
            'payment_processed_at' => date('Y-m-d H:i:s'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ], 'id = ?', [(int) $id]);
        if (!empty($ann['payment_id'])) {
            PaymentService::markPaid((int) $ann['payment_id']);
        }
        Session::flash('success', 'Плащането е потвърдено. Обявата е публикувана.');
        $this->redirect('/admin/announcement-payments/' . $id);
    }

    public function reject(string $id): void
    {
        $ann = $this->findPending((int) $id);
        if (!$ann) {
            $this->redirect('/admin/announcement-payments');
        }
        Database::update('competition_announcements', [
            'payment_status' => 'rejected',
            'payment_admin_notes' => trim($_POST['admin_notes'] ?? '') ?: null,
            'payment_processed_by' => Auth::id(),
            'payment_processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Заявката за публикуване е отхвърлена.');
        $this->redirect('/admin/announcement-payments/' . $id);
    }

    public function destroy(string $id): void
    {
        if (!$this->findPayment((int) $id)) {
            Session::flash('error', 'Плащането не е намерено.');
            $this->redirect('/admin/announcement-payments');
        }
        Database::query('DELETE FROM competition_announcements WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Записът е изтрит.');
        $this->redirect('/admin/announcement-payments');
    }

    /** @return array|null */
    private function findPending(int $id): ?array
    {
        return Database::fetch(
            'SELECT * FROM competition_announcements WHERE id = ? AND payment_status = ?',
            [$id, 'pending']
        );
    }

    /** @return array|null */
    private function findPayment(int $id): ?array
    {
        return Database::fetch(
            "SELECT a.*, u.name AS user_name, u.email, u.phone, u.city,
                    pb.name AS processed_by_name
             FROM competition_announcements a
             JOIN users u ON u.id = a.user_id
             LEFT JOIN users pb ON pb.id = a.payment_processed_by
             WHERE a.id = ? AND a.payment_status != 'not_required'",
            [$id]
        );
    }
}
