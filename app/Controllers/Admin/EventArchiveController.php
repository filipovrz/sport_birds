<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class EventArchiveController extends Controller
{
    public function index(): void
    {
        $base = $this->baseSelectSql();
        $active = Database::fetchAll(
            $base . " WHERE e.event_date >= CURDATE() AND e.status NOT IN ('cancelled', 'completed')
             ORDER BY e.event_date ASC, e.created_at DESC LIMIT 200"
        );
        $archive = Database::fetchAll(
            $base . " WHERE e.event_date < CURDATE() OR e.status IN ('cancelled', 'completed')
             ORDER BY e.event_date DESC, e.created_at DESC LIMIT 300"
        );
        $this->view('admin.event_archive.index', [
            'active' => $active,
            'archive' => $archive,
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $ev = $this->find((int) $id);
        if (!$ev) {
            Session::flash('error', 'Събитието не е намерено.');
            $this->redirect('/admin/event-archive');
        }
        $registrations = Database::fetchAll(
            'SELECT r.*, u.name AS user_name, u.email
             FROM event_registrations r
             JOIN users u ON u.id = r.user_id
             WHERE r.event_id = ?
             ORDER BY r.created_at ASC',
            [(int) $id]
        );
        $this->view('admin.event_archive.show', [
            'ev' => $ev,
            'registrations' => $registrations,
        ], 'layouts.admin');
    }

    public function edit(string $id): void
    {
        $ev = $this->find((int) $id);
        if (!$ev) {
            Session::flash('error', 'Събитието не е намерено.');
            $this->redirect('/admin/event-archive');
        }
        $this->view('admin.event_archive.edit', ['ev' => $ev], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $eventId = (int) $id;
        if (!$this->find($eventId)) {
            Session::flash('error', 'Събитието не е намерено.');
            $this->redirect('/admin/event-archive');
        }
        $d = $this->validate(['title' => 'required', 'event_date' => 'required']);
        Database::update('event_announcements', $this->payload($d), 'id = ?', [$eventId]);
        Session::flash('success', 'Събитието е обновено.');
        $this->redirect('/admin/event-archive/' . $eventId);
    }

    public function print(string $id): void
    {
        $ev = $this->find((int) $id);
        if (!$ev) {
            http_response_code(404);
            echo 'Събитието не е намерено.';
            exit;
        }
        $registrations = Database::fetchAll(
            'SELECT r.*, u.name AS user_name
             FROM event_registrations r
             JOIN users u ON u.id = r.user_id
             WHERE r.event_id = ?
             ORDER BY r.created_at ASC',
            [(int) $id]
        );
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.event_archive.print', [
            'ev' => $ev,
            'registrations' => $registrations,
            'config' => $config,
        ], null);
    }

    public function destroy(string $id): void
    {
        if (!$this->find((int) $id)) {
            Session::flash('error', 'Събитието не е намерено.');
            $this->redirect('/admin/event-archive');
        }
        Database::query('DELETE FROM event_announcements WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Събитието е изтрито.');
        $this->redirect('/admin/event-archive');
    }

    private function baseSelectSql(): string
    {
        return "SELECT e.*, u.name AS user_name, u.email AS user_email,
                (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count
                FROM event_announcements e
                JOIN users u ON u.id = e.user_id";
    }

    /** @return array|null */
    private function find(int $id): ?array
    {
        return Database::fetch($this->baseSelectSql() . ' WHERE e.id = ?', [$id]);
    }

    /** @param array<string, string> $d
     * @return array<string, mixed>
     */
    private function payload(array $d): array
    {
        $status = $_POST['status'] ?? 'published';
        if (!in_array($status, ['draft', 'published', 'cancelled', 'completed'], true)) {
            $status = 'published';
        }
        $paymentStatus = $_POST['payment_status'] ?? 'not_required';
        if (!in_array($paymentStatus, ['not_required', 'pending', 'approved', 'rejected'], true)) {
            $paymentStatus = 'not_required';
        }
        $eventType = $_POST['event_type'] ?? 'gathering';
        if (!in_array($eventType, ['gathering', 'assembly', 'meeting', 'exhibition', 'social', 'other'], true)) {
            $eventType = 'gathering';
        }

        return [
            'title' => $d['title'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'event_type' => $eventType,
            'event_date' => $d['event_date'],
            'event_end_date' => ($_POST['event_end_date'] ?? '') ?: null,
            'registration_deadline' => ($_POST['registration_deadline'] ?? '') ?: null,
            'location' => trim($_POST['location'] ?? '') ?: null,
            'latitude' => ($_POST['latitude'] ?? '') !== '' ? (float) $_POST['latitude'] : null,
            'longitude' => ($_POST['longitude'] ?? '') !== '' ? (float) $_POST['longitude'] : null,
            'organizer' => trim($_POST['organizer'] ?? '') ?: null,
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
            'max_participants' => ($_POST['max_participants'] ?? '') !== '' ? (int) $_POST['max_participants'] : null,
            'attendance_fee_eur' => ($_POST['attendance_fee_eur'] ?? '') !== '' ? (float) $_POST['attendance_fee_eur'] : null,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_reference' => trim($_POST['payment_reference'] ?? '') ?: null,
            'publish_fee_eur' => ($_POST['publish_fee_eur'] ?? '') !== '' ? (float) $_POST['publish_fee_eur'] : null,
            'payment_admin_notes' => trim($_POST['payment_admin_notes'] ?? '') ?: null,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
    }
}
