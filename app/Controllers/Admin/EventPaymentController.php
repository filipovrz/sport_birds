<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\SettingsService;

final class EventPaymentController extends Controller
{
    public function index(): void
    {
        $pending = Database::fetchAll(
            "SELECT e.*, u.name AS user_name, u.email
             FROM event_announcements e
             JOIN users u ON u.id = e.user_id
             WHERE e.payment_status = 'pending'
             ORDER BY e.created_at ASC"
        );
        $history = Database::fetchAll(
            "SELECT e.*, u.name AS user_name, u.email
             FROM event_announcements e
             JOIN users u ON u.id = e.user_id
             WHERE e.payment_status IN ('approved', 'rejected')
             ORDER BY COALESCE(e.payment_processed_at, e.updated_at) DESC
             LIMIT 200"
        );
        $this->view('admin.event_payments.index', [
            'events' => $pending,
            'history' => $history,
            'publishFee' => SettingsService::eventPublishFeeEur(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $ev = $this->findPayment((int) $id);
        if (!$ev) {
            Session::flash('error', 'Плащането не е намерено.');
            $this->redirect('/admin/event-payments');
        }
        $this->view('admin.event_payments.show', ['ev' => $ev], 'layouts.admin');
    }

    public function print(string $id): void
    {
        $ev = $this->findPayment((int) $id);
        if (!$ev) {
            http_response_code(404);
            echo 'Плащането не е намерено.';
            exit;
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.event_payments.print', [
            'ev' => $ev,
            'config' => $config,
            'paymentInstructions' => SettingsService::paymentInstructions(),
        ], null);
    }

    public function approve(string $id): void
    {
        if (!$this->findPending((int) $id)) {
            $this->redirect('/admin/event-payments');
        }
        Database::update('event_announcements', [
            'status' => 'published',
            'payment_status' => 'approved',
            'payment_processed_by' => Auth::id(),
            'payment_processed_at' => date('Y-m-d H:i:s'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Плащането е потвърдено. Обявата е публикувана.');
        $this->redirect('/admin/event-payments/' . $id);
    }

    public function reject(string $id): void
    {
        if (!$this->findPending((int) $id)) {
            $this->redirect('/admin/event-payments');
        }
        Database::update('event_announcements', [
            'payment_status' => 'rejected',
            'payment_admin_notes' => trim($_POST['admin_notes'] ?? '') ?: null,
            'payment_processed_by' => Auth::id(),
            'payment_processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [(int) $id]);
        Session::flash('success', 'Заявката е отхвърлена.');
        $this->redirect('/admin/event-payments/' . $id);
    }

    public function destroy(string $id): void
    {
        if (!$this->findPayment((int) $id)) {
            Session::flash('error', 'Плащането не е намерено.');
            $this->redirect('/admin/event-payments');
        }
        Database::query('DELETE FROM event_announcements WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Записът е изтрит.');
        $this->redirect('/admin/event-payments');
    }

    /** @return array|null */
    private function findPending(int $id): ?array
    {
        return Database::fetch(
            'SELECT * FROM event_announcements WHERE id = ? AND payment_status = ?',
            [$id, 'pending']
        );
    }

    /** @return array|null */
    private function findPayment(int $id): ?array
    {
        return Database::fetch(
            "SELECT e.*, u.name AS user_name, u.email, u.phone, u.city,
                    pb.name AS processed_by_name
             FROM event_announcements e
             JOIN users u ON u.id = e.user_id
             LEFT JOIN users pb ON pb.id = e.payment_processed_by
             WHERE e.id = ? AND e.payment_status != 'not_required'",
            [$id]
        );
    }
}
