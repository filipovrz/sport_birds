<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Bird;
use App\Services\SubscriptionService;

final class AnnouncementController extends Controller
{
    public function index(): void
    {
        $items = Database::fetchAll(
            "SELECT a.*, u.name AS publisher_name, u.club_name AS publisher_club,
             (SELECT COUNT(*) FROM competition_registrations r WHERE r.announcement_id = a.id) AS reg_count
             FROM competition_announcements a
             JOIN users u ON u.id = a.user_id
             WHERE a.status = 'published' AND a.event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             ORDER BY a.is_featured DESC, a.event_date ASC"
        );
        $this->view('announcements.index', ['announcements' => $items], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function show(string $id): void
    {
        $a = Database::fetch(
            'SELECT a.*, u.name AS publisher_name, u.email AS publisher_email, u.club_name AS publisher_club
             FROM competition_announcements a JOIN users u ON u.id = a.user_id
             WHERE a.id = ? AND a.status = \'published\'',
            [(int) $id]
        );
        if (!$a) {
            App::notFound();
        }
        $regs = Database::fetchAll(
            'SELECT r.*, u.name AS user_name, b.ring_number FROM competition_registrations r
             JOIN users u ON u.id = r.user_id
             LEFT JOIN birds b ON b.id = r.bird_id
             WHERE r.announcement_id = ? ORDER BY r.created_at',
            [(int) $id]
        );
        $myReg = Auth::check()
            ? Database::fetch('SELECT * FROM competition_registrations WHERE announcement_id = ? AND user_id = ?', [(int) $id, Auth::id()])
            : null;
        $this->view('announcements.show', [
            'a' => $a,
            'registrations' => $regs,
            'myReg' => $myReg,
            'birds' => Auth::check() ? Bird::forUser(Auth::id()) : [],
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function create(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->view('announcements.form', ['item' => null]);
    }

    public function store(): void
    {
        if (!SubscriptionService::hasFeature('announcements') && !Auth::isAdmin()) {
            Session::flash('error', 'Публикуване на обяви изисква Pro план.');
            $this->redirect('/dashboard/subscription');
        }
        $d = $this->validate(['title' => 'required', 'event_date' => 'required']);
        $id = Database::insert('competition_announcements', $this->announcementData($d));
        Session::flash('success', 'Обявата е публикувана.');
        $this->redirect('/announcements/' . $id);
    }

    public function register(string $id): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $birdId = ($_POST['bird_id'] ?? '') ? (int) $_POST['bird_id'] : null;
        $sql = 'SELECT id FROM competition_registrations WHERE announcement_id = ? AND user_id = ?';
        $params = [(int) $id, Auth::id()];
        if ($birdId) {
            $sql .= ' AND bird_id = ?';
            $params[] = $birdId;
        } else {
            $sql .= ' AND bird_id IS NULL';
        }
        $exists = Database::fetch($sql, $params);
        if ($exists) {
            Session::flash('error', 'Вече сте записани.');
            $this->back();
        }
        Database::insert('competition_registrations', [
            'announcement_id' => (int) $id,
            'user_id' => Auth::id(),
            'bird_id' => ($_POST['bird_id'] ?? '') ? (int) $_POST['bird_id'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Успешна регистрация за състезанието.');
        $this->redirect('/announcements/' . $id);
    }

    public function my(): void
    {
        $items = Database::fetchAll(
            'SELECT * FROM competition_announcements WHERE user_id = ? ORDER BY created_at DESC',
            [Auth::id()]
        );
        $this->view('announcements.my', ['announcements' => $items]);
    }

    /** @param array<string, string> $d */
    private function announcementData(array $d): array
    {
        return [
            'user_id' => Auth::id(),
            'title' => $d['title'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'competition_type' => $_POST['competition_type'] ?? 'race',
            'species' => $_POST['species'] ?? 'racing_pigeon',
            'event_date' => $d['event_date'],
            'registration_deadline' => ($_POST['registration_deadline'] ?? '') ?: null,
            'location' => trim($_POST['location'] ?? '') ?: null,
            'latitude' => ($_POST['latitude'] ?? '') !== '' ? (float) $_POST['latitude'] : null,
            'longitude' => ($_POST['longitude'] ?? '') !== '' ? (float) $_POST['longitude'] : null,
            'distance_km' => ($_POST['distance_km'] ?? '') ?: null,
            'organizer' => trim($_POST['organizer'] ?? '') ?: null,
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
            'max_participants' => ($_POST['max_participants'] ?? '') ? (int) $_POST['max_participants'] : null,
            'entry_fee_bgn' => ($_POST['entry_fee_bgn'] ?? '') ?: null,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'status' => 'published',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
    }
}
