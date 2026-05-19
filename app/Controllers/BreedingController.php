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

final class BreedingController extends Controller
{
    public function index(): void
    {
        if (!SubscriptionService::hasFeature('breeding')) {
            Session::flash('error', 'Развъждането изисква платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $pairs = Database::fetchAll(
            'SELECT bp.*, m.ring_number AS male_ring, f.ring_number AS female_ring
             FROM breeding_pairs bp
             JOIN birds m ON m.id = bp.male_bird_id
             JOIN birds f ON f.id = bp.female_bird_id
             WHERE bp.user_id = ? ORDER BY bp.season_year DESC',
            [Auth::id()]
        );
        $this->view('breeding.index', ['pairs' => $pairs]);
    }

    public function create(): void
    {
        if (!SubscriptionService::hasFeature('breeding')) {
            Session::flash('error', 'Развъждането изисква платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $this->view('breeding.form', [
            'birds' => Bird::parentsOptions(Auth::id()),
        ]);
    }

    public function store(): void
    {
        $male = (int) ($_POST['male_bird_id'] ?? 0);
        $female = (int) ($_POST['female_bird_id'] ?? 0);
        Database::insert('breeding_pairs', [
            'user_id' => Auth::id(),
            'male_bird_id' => $male,
            'female_bird_id' => $female,
            'season_year' => (int) ($_POST['season_year'] ?? date('Y')),
            'paired_at' => ($_POST['paired_at'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        if (($_POST['laid_at'] ?? '') !== '') {
            $pairId = (int) Database::connection()->lastInsertId();
            Database::insert('breeding_clutches', [
                'breeding_pair_id' => $pairId,
                'laid_at' => $_POST['laid_at'],
                'egg_count' => ($_POST['egg_count'] ?? '') ? (int) $_POST['egg_count'] : null,
            ]);
        }
        Session::flash('success', 'Развъдна двойка е записана.');
        $this->redirect('/dashboard/breeding');
    }

    public function show(string $id): void
    {
        $pair = Database::fetch(
            'SELECT bp.*, m.ring_number AS male_ring, f.ring_number AS female_ring
             FROM breeding_pairs bp
             JOIN birds m ON m.id = bp.male_bird_id
             JOIN birds f ON f.id = bp.female_bird_id
             WHERE bp.id = ? AND bp.user_id = ?',
            [(int) $id, Auth::id()]
        );
        if (!$pair) {
            App::notFound();
        }
        $clutches = Database::fetchAll(
            'SELECT * FROM breeding_clutches WHERE breeding_pair_id = ?',
            [(int) $id]
        );
        $this->view('breeding.show', ['pair' => $pair, 'clutches' => $clutches]);
    }
}
