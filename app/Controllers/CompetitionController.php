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

final class CompetitionController extends Controller
{
    public function index(): void
    {
        if (!SubscriptionService::hasFeature('competitions')) {
            Session::flash('error', 'Състезанията изискват платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $items = Database::fetchAll(
            'SELECT * FROM competitions WHERE user_id = ? ORDER BY event_date DESC',
            [Auth::id()]
        );
        $this->view('competitions.index', ['competitions' => $items]);
    }

    public function create(): void
    {
        if (!SubscriptionService::hasFeature('competitions')) {
            Session::flash('error', 'Състезанията изискват платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $this->view('competitions.form', ['competition' => null]);
    }

    public function store(): void
    {
        $d = $this->validate(['name' => 'required', 'event_date' => 'required']);
        $id = Database::insert('competitions', [
            'user_id' => Auth::id(),
            'name' => $d['name'],
            'competition_type' => $_POST['competition_type'] ?? 'race',
            'species' => $_POST['species'] ?? 'racing_pigeon',
            'event_date' => $d['event_date'],
            'location' => trim($_POST['location'] ?? '') ?: null,
            'distance_km' => ($_POST['distance_km'] ?? '') ?: null,
            'organizer' => trim($_POST['organizer'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Състезанието е създадено.');
        $this->redirect('/dashboard/competitions/' . $id);
    }

    public function show(string $id): void
    {
        $comp = Database::fetch('SELECT * FROM competitions WHERE id = ? AND user_id = ?', [(int) $id, Auth::id()]);
        if (!$comp) {
            App::notFound();
        }
        $results = Database::fetchAll(
            'SELECT r.*, b.ring_number, b.name AS bird_name FROM competition_results r
             JOIN birds b ON b.id = r.bird_id WHERE r.competition_id = ? ORDER BY r.position',
            [(int) $id]
        );
        $this->view('competitions.show', [
            'competition' => $comp,
            'results' => $results,
            'birds' => Bird::forUser(Auth::id()),
        ]);
    }

    public function storeResult(string $id): void
    {
        $comp = Database::fetch('SELECT id FROM competitions WHERE id = ? AND user_id = ?', [(int) $id, Auth::id()]);
        if (!$comp) {
            App::notFound();
        }
        $bird = Bird::findOwned((int) $_POST['bird_id'], Auth::id());
        if (!$bird) {
            Session::flash('error', 'Невалидна птица.');
            $this->back();
        }
        Database::insert('competition_results', [
            'competition_id' => (int) $id,
            'bird_id' => (int) $_POST['bird_id'],
            'position' => ($_POST['position'] ?? '') ? (int) $_POST['position'] : null,
            'velocity_mpm' => ($_POST['velocity_mpm'] ?? '') ?: null,
            'points' => ($_POST['points'] ?? '') ?: null,
            'prize' => trim($_POST['prize'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Резултатът е добавен.');
        $this->redirect('/dashboard/competitions/' . $id);
    }
}
