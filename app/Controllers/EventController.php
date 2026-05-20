<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\SettingsService;

final class EventController extends Controller
{
    public function index(): void
    {
        $items = Database::fetchAll(
            "SELECT e.*, u.name AS publisher_name, u.club_name AS publisher_club,
             (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count
             FROM event_announcements e
             JOIN users u ON u.id = e.user_id
             WHERE e.status = 'published' AND e.event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             ORDER BY e.is_featured DESC, e.event_date ASC"
        );
        $myRegIds = [];
        if (Auth::check()) {
            $rows = Database::fetchAll(
                'SELECT event_id FROM event_registrations WHERE user_id = ?',
                [Auth::id()]
            );
            $myRegIds = array_map('intval', array_column($rows, 'event_id'));
        }
        $this->view('events.index', [
            'events' => $items,
            'publishFee' => SettingsService::eventPublishFeeEur(),
            'myRegIds' => $myRegIds,
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function show(string $id): void
    {
        $e = Database::fetch(
            'SELECT e.*, u.name AS publisher_name, u.email AS publisher_email, u.club_name AS publisher_club
             FROM event_announcements e JOIN users u ON u.id = e.user_id
             WHERE e.id = ?',
            [(int) $id]
        );
        if (!$e) {
            App::notFound();
        }
        $isOwner = Auth::check() && (int) $e['user_id'] === Auth::id();
        if ($e['status'] !== 'published' && !Auth::isAdmin() && !$isOwner) {
            App::notFound();
        }
        $regs = Database::fetchAll(
            'SELECT r.*, u.name AS user_name FROM event_registrations r
             JOIN users u ON u.id = r.user_id
             WHERE r.event_id = ? ORDER BY r.created_at',
            [(int) $id]
        );
        $myReg = Auth::check()
            ? Database::fetch('SELECT * FROM event_registrations WHERE event_id = ? AND user_id = ?', [(int) $id, Auth::id()])
            : null;
        $canRegister = Auth::check()
            && !$myReg
            && ($e['status'] ?? '') === 'published'
            && !$isOwner
            && (empty($e['registration_deadline']) || $e['registration_deadline'] >= date('Y-m-d'))
            && $e['event_date'] >= date('Y-m-d');
        $this->view('events.show', [
            'e' => $e,
            'registrations' => $regs,
            'myReg' => $myReg,
            'isOwner' => $isOwner,
            'canRegister' => $canRegister,
        ], Auth::check() ? 'layouts.app' : 'layouts.guest');
    }

    public function create(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $fee = SettingsService::eventPublishFeeEur();
        $this->view('events.form', [
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
        $fee = Auth::isAdmin() ? 0.0 : SettingsService::eventPublishFeeEur();
        $requiresPayment = $fee > 0;

        if ($requiresPayment && trim($_POST['payment_method'] ?? '') === '') {
            Session::flash('error', 'Изберете начин на плащане.');
            Session::flash('old', $_POST);
            $this->redirect('/dashboard/events/create');
        }

        $data = $this->eventData($d);
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

        $id = Database::insert('event_announcements', $data);
        if ($requiresPayment) {
            \App\Services\CheckoutFlowService::start(
                \App\Services\PaymentService::PAYABLE_EVENT,
                $id,
                $fee,
                'Публикуване събитие: ' . ($data['title'] ?? ''),
                \App\Services\CheckoutFlowService::paymentMethodFromPost()
            );
        }
        Session::flash('success', 'Обявата за събитие е публикувана.');
        $this->redirect('/events/' . $id);
    }

    public function register(string $id): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Влезте в профила си, за да се запишете.');
            $this->redirect('/login');
        }
        $eventId = (int) $id;
        $ev = Database::fetch(
            'SELECT id, user_id, status, event_date, registration_deadline, max_participants
             FROM event_announcements WHERE id = ?',
            [$eventId]
        );
        if (!$ev || $ev['status'] !== 'published') {
            Session::flash('error', 'Обявата не е активна.');
            $this->redirect('/events');
        }
        if ((int) $ev['user_id'] === Auth::id()) {
            Session::flash('error', 'Не можете да се запишете за собствено събитие.');
            $this->redirect('/events/' . $eventId);
        }
        if (!empty($ev['registration_deadline']) && $ev['registration_deadline'] < date('Y-m-d')) {
            Session::flash('error', 'Крайният срок за запис е изтекъл.');
            $this->redirect('/events/' . $eventId);
        }
        if ($ev['event_date'] < date('Y-m-d')) {
            Session::flash('error', 'Събитието вече е минало.');
            $this->redirect('/events/' . $eventId);
        }
        if (!empty($ev['max_participants'])) {
            $count = (int) Database::fetch(
                'SELECT COUNT(*) AS c FROM event_registrations WHERE event_id = ?',
                [$eventId]
            )['c'];
            if ($count >= (int) $ev['max_participants']) {
                Session::flash('error', 'Достигнат е максималният брой участници.');
                $this->redirect('/events/' . $eventId);
            }
        }
        $exists = Database::fetch(
            'SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?',
            [$eventId, Auth::id()]
        );
        if ($exists) {
            Session::flash('error', 'Вече сте записани за това събитие.');
            $this->redirect('/events/' . $eventId);
        }
        Database::insert('event_registrations', [
            'event_id' => $eventId,
            'user_id' => Auth::id(),
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Успешно се записахте за събитието.');
        $this->redirect('/events/' . $eventId);
    }

    public function my(): void
    {
        $items = Database::fetchAll(
            'SELECT * FROM event_announcements WHERE user_id = ? ORDER BY created_at DESC',
            [Auth::id()]
        );
        $this->view('events.my', ['events' => $items]);
    }

    /** @param array<string, string> $d
     * @return array<string, mixed>
     */
    private function eventData(array $d): array
    {
        return [
            'user_id' => Auth::id(),
            'title' => $d['title'],
            'description' => trim($_POST['description'] ?? '') ?: null,
            'event_type' => $_POST['event_type'] ?? 'gathering',
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
            'is_featured' => Auth::isAdmin() && isset($_POST['is_featured']) ? 1 : 0,
        ];
    }
}
