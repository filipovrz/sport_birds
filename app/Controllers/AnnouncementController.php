<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Bird;
use App\Services\GeocodingService;
use App\Services\SettingsService;

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
        $myRegIds = [];
        if (Auth::check()) {
            $rows = Database::fetchAll(
                'SELECT announcement_id FROM competition_registrations WHERE user_id = ?',
                [Auth::id()]
            );
            $myRegIds = array_map('intval', array_column($rows, 'announcement_id'));
        }
        $this->view('announcements.index', [
            'announcements' => $items,
            'publishFee' => SettingsService::announcementPublishFeeEur(),
            'myRegIds' => $myRegIds,
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function show(string $id): void
    {
        $a = Database::fetch(
            'SELECT a.*, u.name AS publisher_name, u.email AS publisher_email, u.club_name AS publisher_club
             FROM competition_announcements a JOIN users u ON u.id = a.user_id
             WHERE a.id = ?',
            [(int) $id]
        );
        if (!$a) {
            App::notFound();
        }
        $isOwner = Auth::check() && (int) $a['user_id'] === Auth::id();
        if ($a['status'] !== 'published' && !Auth::isAdmin() && !$isOwner) {
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
        $canRegister = Auth::check()
            && !$myReg
            && ($a['status'] ?? '') === 'published'
            && !$isOwner
            && (empty($a['registration_deadline']) || $a['registration_deadline'] >= date('Y-m-d'))
            && $a['event_date'] >= date('Y-m-d');
        $this->view('announcements.show', [
            'a' => $a,
            'registrations' => $regs,
            'myReg' => $myReg,
            'birds' => Auth::check() ? Bird::forUser(Auth::id()) : [],
            'isOwner' => $isOwner,
            'canRegister' => $canRegister,
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function create(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $fee = SettingsService::announcementPublishFeeEur();
        $this->view('announcements.form', [
            'item' => null,
            'publishFee' => $fee,
            'paymentInstructions' => SettingsService::paymentInstructions(),
            'paymentMethods' => \App\Services\CheckoutFlowService::methodsForForms(),
            'requiresPayment' => !Auth::isAdmin() && $fee > 0,
        ]);
    }

    public function store(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $d = $this->validate(['title' => 'required', 'event_date' => 'required']);
        $fee = Auth::isAdmin() ? 0.0 : SettingsService::announcementPublishFeeEur();
        $requiresPayment = $fee > 0;

        if ($requiresPayment && trim($_POST['payment_method'] ?? '') === '') {
            Session::flash('error', 'Изберете начин на плащане.');
            Session::flash('old', $_POST);
            $this->redirect('/dashboard/announcements/create');
        }

        $data = $this->announcementData($d);
        if ($requiresPayment) {
            $data['status'] = 'draft';
            $data['payment_status'] = 'pending';
            $data['publish_fee_eur'] = $fee;
            $data['is_featured'] = 0;
        } else {
            $data['status'] = 'published';
            $data['payment_status'] = 'not_required';
            $data['publish_fee_eur'] = null;
            $data['payment_reference'] = null;
            if (!Auth::isAdmin()) {
                $data['is_featured'] = 0;
            }
        }

        $id = Database::insert('competition_announcements', $data);
        if ($requiresPayment) {
            \App\Services\CheckoutFlowService::start(
                \App\Services\PaymentService::PAYABLE_COMPETITION,
                $id,
                $fee,
                'Публикуване обява: ' . ($data['title'] ?? ''),
                \App\Services\CheckoutFlowService::paymentMethodFromPost()
            );
        }
        Session::flash('success', 'Обявата е публикувана.');
        $this->redirect('/announcements/' . $id);
    }

    public function register(string $id): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Влезте в профила си, за да се запишете за състезание.');
            $this->redirect('/login');
        }
        $annId = (int) $id;
        $ann = Database::fetch(
            'SELECT id, user_id, status, event_date, registration_deadline, max_participants
             FROM competition_announcements WHERE id = ?',
            [$annId]
        );
        if (!$ann || $ann['status'] !== 'published') {
            Session::flash('error', 'Обявата не е активна.');
            $this->redirect('/announcements');
        }
        if ((int) $ann['user_id'] === Auth::id()) {
            Session::flash('error', 'Не можете да се запишете за собствена обява.');
            $this->redirect('/announcements/' . $annId);
        }
        if (!empty($ann['registration_deadline']) && $ann['registration_deadline'] < date('Y-m-d')) {
            Session::flash('error', 'Крайният срок за запис е изтекъл.');
            $this->redirect('/announcements/' . $annId);
        }
        if ($ann['event_date'] < date('Y-m-d')) {
            Session::flash('error', 'Състезанието вече е минало.');
            $this->redirect('/announcements/' . $annId);
        }
        if (!empty($ann['max_participants'])) {
            $count = (int) Database::fetch(
                'SELECT COUNT(*) AS c FROM competition_registrations WHERE announcement_id = ?',
                [$annId]
            )['c'];
            if ($count >= (int) $ann['max_participants']) {
                Session::flash('error', 'Достигнат е максималният брой участници.');
                $this->redirect('/announcements/' . $annId);
            }
        }
        $birdId = ($_POST['bird_id'] ?? '') !== '' ? (int) $_POST['bird_id'] : null;
        if ($birdId) {
            $bird = Bird::findOwned($birdId, Auth::id());
            if (!$bird) {
                Session::flash('error', 'Избраната птица не е намерена.');
                $this->redirect('/announcements/' . $annId);
            }
        }
        $exists = Database::fetch(
            'SELECT id FROM competition_registrations WHERE announcement_id = ? AND user_id = ?',
            [$annId, Auth::id()]
        );
        if ($exists) {
            Session::flash('error', 'Вече сте записани за това състезание.');
            $this->redirect('/announcements/' . $annId);
        }
        Database::insert('competition_registrations', [
            'announcement_id' => $annId,
            'user_id' => Auth::id(),
            'bird_id' => $birdId,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Успешно се записахте за състезанието.');
        $this->redirect('/announcements/' . $annId);
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
    /** @return array<string, mixed> */
    private function announcementData(array $d): array
    {
        $location = trim($_POST['location'] ?? '') ?: null;
        $lat = ($_POST['latitude'] ?? '') !== '' ? (float) $_POST['latitude'] : null;
        $lng = ($_POST['longitude'] ?? '') !== '' ? (float) $_POST['longitude'] : null;
        if (($lat === null || $lng === null) && $location) {
            $coords = GeocodingService::resolve($location, $lat, $lng);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        return [
            'user_id' => Auth::id(),
            'title' => $d['title'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'competition_type' => $_POST['competition_type'] ?? 'race',
            'species' => $_POST['species'] ?? 'racing_pigeon',
            'event_date' => $d['event_date'],
            'registration_deadline' => ($_POST['registration_deadline'] ?? '') ?: null,
            'location' => $location,
            'latitude' => $lat,
            'longitude' => $lng,
            'distance_km' => ($_POST['distance_km'] ?? '') ?: null,
            'organizer' => trim($_POST['organizer'] ?? '') ?: null,
            'club_name' => trim($_POST['club_name'] ?? '') ?: null,
            'max_participants' => ($_POST['max_participants'] ?? '') ? (int) $_POST['max_participants'] : null,
            'entry_fee_bgn' => ($_POST['entry_fee_bgn'] ?? '') ?: null,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'is_featured' => Auth::isAdmin() && isset($_POST['is_featured']) ? 1 : 0,
        ];
    }
}
