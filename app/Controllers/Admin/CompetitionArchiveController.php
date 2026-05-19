<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class CompetitionArchiveController extends Controller
{
    public function index(): void
    {
        $base = $this->baseSelectSql();
        $active = Database::fetchAll(
            $base . " WHERE a.event_date >= CURDATE() AND a.status NOT IN ('cancelled', 'completed')
             ORDER BY a.event_date ASC, a.created_at DESC LIMIT 200"
        );
        $archive = Database::fetchAll(
            $base . " WHERE a.event_date < CURDATE() OR a.status IN ('cancelled', 'completed')
             ORDER BY a.event_date DESC, a.created_at DESC LIMIT 300"
        );
        $this->view('admin.competition_archive.index', [
            'active' => $active,
            'archive' => $archive,
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $ann = $this->find((int) $id);
        if (!$ann) {
            Session::flash('error', 'Състезанието не е намерено.');
            $this->redirect('/admin/competition-archive');
        }
        $registrations = Database::fetchAll(
            'SELECT r.*, u.name AS user_name, u.email, b.ring_number
             FROM competition_registrations r
             JOIN users u ON u.id = r.user_id
             LEFT JOIN birds b ON b.id = r.bird_id
             WHERE r.announcement_id = ?
             ORDER BY r.created_at ASC',
            [(int) $id]
        );
        $this->view('admin.competition_archive.show', [
            'ann' => $ann,
            'registrations' => $registrations,
        ], 'layouts.admin');
    }

    public function edit(string $id): void
    {
        $ann = $this->find((int) $id);
        if (!$ann) {
            Session::flash('error', 'Състезанието не е намерено.');
            $this->redirect('/admin/competition-archive');
        }
        $this->view('admin.competition_archive.edit', ['ann' => $ann], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $annId = (int) $id;
        if (!$this->find($annId)) {
            Session::flash('error', 'Състезанието не е намерено.');
            $this->redirect('/admin/competition-archive');
        }
        $d = $this->validate(['title' => 'required', 'event_date' => 'required']);
        Database::update('competition_announcements', $this->payload($d), 'id = ?', [$annId]);
        Session::flash('success', 'Състезанието е обновено.');
        $this->redirect('/admin/competition-archive/' . $annId);
    }

    public function print(string $id): void
    {
        $ann = $this->find((int) $id);
        if (!$ann) {
            http_response_code(404);
            echo 'Състезанието не е намерено.';
            exit;
        }
        $registrations = Database::fetchAll(
            'SELECT r.*, u.name AS user_name, b.ring_number
             FROM competition_registrations r
             JOIN users u ON u.id = r.user_id
             LEFT JOIN birds b ON b.id = r.bird_id
             WHERE r.announcement_id = ?
             ORDER BY r.created_at ASC',
            [(int) $id]
        );
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.competition_archive.print', [
            'ann' => $ann,
            'registrations' => $registrations,
            'config' => $config,
        ], null);
    }

    public function destroy(string $id): void
    {
        if (!$this->find((int) $id)) {
            Session::flash('error', 'Състезанието не е намерено.');
            $this->redirect('/admin/competition-archive');
        }
        Database::query('DELETE FROM competition_announcements WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Състезанието е изтрито.');
        $this->redirect('/admin/competition-archive');
    }

    private function baseSelectSql(): string
    {
        return "SELECT a.*, u.name AS user_name, u.email AS user_email,
                (SELECT COUNT(*) FROM competition_registrations r WHERE r.announcement_id = a.id) AS reg_count
                FROM competition_announcements a
                JOIN users u ON u.id = a.user_id";
    }

    /** @return array|null */
    private function find(int $id): ?array
    {
        return Database::fetch(
            $this->baseSelectSql() . ' WHERE a.id = ?',
            [$id]
        );
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

        return [
            'title' => $d['title'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'competition_type' => $_POST['competition_type'] ?? 'race',
            'species' => $_POST['species'] ?? 'racing_pigeon',
            'event_date' => $d['event_date'],
            'registration_deadline' => ($_POST['registration_deadline'] ?? '') ?: null,
            'location' => trim($_POST['location'] ?? '') ?: null,
            'latitude' => ($_POST['latitude'] ?? '') !== '' ? (float) $_POST['latitude'] : null,
            'longitude' => ($_POST['longitude'] ?? '') !== '' ? (float) $_POST['longitude'] : null,
            'distance_km' => ($_POST['distance_km'] ?? '') !== '' ? (float) $_POST['distance_km'] : null,
            'organizer' => trim($_POST['organizer'] ?? '') ?: null,
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
            'max_participants' => ($_POST['max_participants'] ?? '') !== '' ? (int) $_POST['max_participants'] : null,
            'entry_fee_bgn' => ($_POST['entry_fee_bgn'] ?? '') !== '' ? (float) $_POST['entry_fee_bgn'] : null,
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
