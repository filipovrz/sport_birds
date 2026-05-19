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
            Session::flash('error', 'Лимит на птичарници за вашия план.');
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
            'latitude' => ($_POST['latitude'] ?? '') ?: null,
            'longitude' => ($_POST['longitude'] ?? '') ?: null,
            'capacity' => ($_POST['capacity'] ?? '') ? (int) $_POST['capacity'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Птичарникът е създаден.');
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
            'latitude' => ($_POST['latitude'] ?? '') !== '' ? $_POST['latitude'] : null,
            'longitude' => ($_POST['longitude'] ?? '') !== '' ? $_POST['longitude'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Птичарникът е обновен.');
        $this->redirect('/dashboard/lofts/' . $id);
    }

    public function destroy(string $id): void
    {
        Loft::delete((int) $id, Auth::id());
        Session::flash('success', 'Изтрит.');
        $this->redirect('/dashboard/lofts');
    }
}
