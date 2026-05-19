<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Bird;
use App\Models\Loft;

final class HealthController extends Controller
{
    public function index(): void
    {
        $records = Database::fetchAll(
            'SELECT h.*, b.ring_number FROM health_records h
             LEFT JOIN birds b ON b.id = h.bird_id
             WHERE h.user_id = ? ORDER BY h.recorded_at DESC',
            [Auth::id()]
        );
        $this->view('health.index', ['records' => $records]);
    }

    public function create(): void
    {
        $this->view('health.form', [
            'record' => null,
            'birds' => Bird::forUser(Auth::id()),
            'lofts' => Loft::forUser(Auth::id()),
        ]);
    }

    public function store(): void
    {
        $d = $this->validate(['title' => 'required', 'recorded_at' => 'required']);
        Database::insert('health_records', $this->healthData($d));
        Session::flash('success', 'Здравен запис е добавен.');
        $this->redirect('/dashboard/health');
    }

    public function edit(string $id): void
    {
        $record = Database::fetch('SELECT * FROM health_records WHERE id = ? AND user_id = ?', [(int) $id, Auth::id()]);
        if (!$record) {
            http_response_code(404);
            exit;
        }
        $this->view('health.form', [
            'record' => $record,
            'birds' => Bird::forUser(Auth::id()),
            'lofts' => Loft::forUser(Auth::id()),
        ]);
    }

    public function update(string $id): void
    {
        $d = $this->validate(['title' => 'required']);
        Database::update('health_records', $this->healthData($d), 'id = ? AND user_id = ?', [(int) $id, Auth::id()]);
        Session::flash('success', 'Обновено.');
        $this->redirect('/dashboard/health');
    }

    /** @param array<string, string> $d */
    private function healthData(array $d): array
    {
        return [
            'user_id' => Auth::id(),
            'bird_id' => ($_POST['bird_id'] ?? '') ? (int) $_POST['bird_id'] : null,
            'loft_id' => ($_POST['loft_id'] ?? '') ? (int) $_POST['loft_id'] : null,
            'record_type' => $_POST['record_type'] ?? 'other',
            'title' => $d['title'],
            'diagnosis' => trim($_POST['diagnosis'] ?? '') ?: null,
            'treatment' => trim($_POST['treatment'] ?? '') ?: null,
            'medication' => trim($_POST['medication'] ?? '') ?: null,
            'veterinarian' => trim($_POST['veterinarian'] ?? '') ?: null,
            'recorded_at' => $d['recorded_at'] ?? $_POST['recorded_at'],
            'next_due_at' => ($_POST['next_due_at'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ];
    }
}
