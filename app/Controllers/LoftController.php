<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bird;
use App\Models\Loft;
use App\Services\SubscriptionService;

final class LoftController extends Controller
{
    public function index(): void
    {
        $this->view('lofts.index', ['lofts' => Loft::forUser(Auth::id())]);
    }

    public function create(): void
    {
        if (!SubscriptionService::canAddLoft(Auth::id())) {
            Session::flash('error', 'Лимит на гълъбарници за вашия план.');
            $this->redirect('/dashboard/subscription');
        }
        $this->view('lofts.form', ['loft' => null]);
    }

    public function store(): void
    {
        if (!SubscriptionService::canAddLoft(Auth::id())) {
            $this->redirect('/dashboard/subscription');
        }
        $d = $this->validate(['name' => 'required']);
        Loft::create([
            'user_id' => Auth::id(),
            'name' => $d['name'],
            'location' => trim($_POST['location'] ?? '') ?: null,
            'latitude' => $this->coordFromPost('latitude'),
            'longitude' => $this->coordFromPost('longitude'),
            'capacity' => ($_POST['capacity'] ?? '') ? (int) $_POST['capacity'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'is_public' => isset($_POST['is_public']) ? 1 : (!empty(Auth::user()['default_public_lofts']) ? 1 : 0),
        ]);
        Session::flash('success', 'Гълъбарникът е създаден.');
        $this->redirect('/dashboard/lofts');
    }

    public function show(string $id): void
    {
        $loft = Loft::findOwned((int) $id, Auth::id());
        if (!$loft) {
            http_response_code(404);
            exit;
        }
        $this->view('lofts.show', [
            'loft' => $loft,
            'birds' => Bird::forUser(Auth::id(), (int) $id),
        ]);
    }

    public function edit(string $id): void
    {
        $loft = Loft::findOwned((int) $id, Auth::id());
        if (!$loft) {
            http_response_code(404);
            exit;
        }
        $this->view('lofts.form', ['loft' => $loft]);
    }

    public function update(string $id): void
    {
        $loft = Loft::findOwned((int) $id, Auth::id());
        if (!$loft) {
            http_response_code(404);
            exit;
        }
        $d = $this->validate(['name' => 'required']);
        Loft::update((int) $id, [
            'name' => $d['name'],
            'location' => trim($_POST['location'] ?? '') ?: null,
            'latitude' => $this->coordFromPost('latitude'),
            'longitude' => $this->coordFromPost('longitude'),
            'capacity' => ($_POST['capacity'] ?? '') !== '' ? (int) $_POST['capacity'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
        ]);
        Session::flash('success', 'Гълъбарникът е обновен.');
        $this->redirect('/dashboard/lofts/' . $id);
    }

    public function destroy(string $id): void
    {
        Loft::delete((int) $id, Auth::id());
        Session::flash('success', 'Изтрит.');
        $this->redirect('/dashboard/lofts');
    }

    private function coordFromPost(string $field): ?float
    {
        $v = trim((string) ($_POST[$field] ?? ''));
        if ($v === '') {
            return null;
        }
        if (!is_numeric($v)) {
            return null;
        }

        return (float) $v;
    }

}
