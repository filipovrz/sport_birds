<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Bird;
use App\Models\Loft;
use App\Services\SubscriptionService;

final class TrainingController extends Controller
{
    public function index(): void
    {
        if (!SubscriptionService::hasFeature('training')) {
            Session::flash('error', 'Тренировките изискват платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $sessions = Database::fetchAll(
            'SELECT t.*, l.name AS loft_name FROM training_sessions t
             LEFT JOIN lofts l ON l.id = t.loft_id
             WHERE t.user_id = ? ORDER BY t.session_date DESC',
            [Auth::id()]
        );
        $this->view('training.index', ['sessions' => $sessions]);
    }

    public function create(): void
    {
        $this->view('training.form', [
            'lofts' => Loft::forUser(Auth::id()),
            'birds' => Bird::forUser(Auth::id()),
        ]);
    }

    public function store(): void
    {
        $sessionId = Database::insert('training_sessions', [
            'user_id' => Auth::id(),
            'loft_id' => ($_POST['loft_id'] ?? '') ? (int) $_POST['loft_id'] : null,
            'session_date' => $_POST['session_date'] ?? date('Y-m-d'),
            'duration_minutes' => ($_POST['duration_minutes'] ?? '') ? (int) $_POST['duration_minutes'] : null,
            'distance_km' => ($_POST['distance_km'] ?? '') ?: null,
            'weather' => trim($_POST['weather'] ?? '') ?: null,
            'birds_released' => ($_POST['birds_released'] ?? '') ? (int) $_POST['birds_released'] : null,
            'birds_returned' => ($_POST['birds_returned'] ?? '') ? (int) $_POST['birds_returned'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        foreach ($_POST['bird_ids'] ?? [] as $birdId) {
            Database::insert('training_birds', [
                'training_session_id' => $sessionId,
                'bird_id' => (int) $birdId,
            ]);
        }
        Session::flash('success', 'Тренировката е записана.');
        $this->redirect('/dashboard/training');
    }
}
