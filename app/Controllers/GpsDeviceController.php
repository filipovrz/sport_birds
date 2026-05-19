<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bird;
use App\Models\GpsDevice;
use App\Services\SubscriptionService;

final class GpsDeviceController extends Controller
{
    public function index(): void
    {
        if (!SubscriptionService::hasFeature('gps_tracking')) {
            Session::flash('error', 'GPS проследяването изисква платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $this->view('gps.index', ['devices' => GpsDevice::forUser(Auth::id())]);
    }

    public function create(): void
    {
        $this->view('gps.form', [
            'device' => null,
            'birds' => Bird::forUser(Auth::id()),
        ]);
    }

    public function store(): void
    {
        $d = $this->validate(['name' => 'required', 'serial_number' => 'required']);
        try {
            GpsDevice::create([
                'user_id' => Auth::id(),
                'name' => $d['name'],
                'serial_number' => $d['serial_number'],
                'model' => trim($_POST['model'] ?? '') ?: null,
                'bird_id' => ($_POST['bird_id'] ?? '') ? (int) $_POST['bird_id'] : null,
                'notes' => trim($_POST['notes'] ?? '') ?: null,
            ]);
            Session::flash('success', 'GPS устройството е регистрирано.');
            $this->redirect('/dashboard/gps');
        } catch (\PDOException) {
            Session::flash('error', 'Серийният номер вече е регистриран.');
            $this->back();
        }
    }

    public function show(string $id): void
    {
        $device = GpsDevice::findOwned((int) $id, Auth::id());
        if (!$device) {
            App::notFound();
        }
        $this->view('gps.show', [
            'device' => $device,
            'history' => GpsDevice::trackHistory((int) $id, Auth::id()),
        ]);
    }

    public function edit(string $id): void
    {
        $device = GpsDevice::findOwned((int) $id, Auth::id());
        if (!$device) {
            App::notFound();
        }
        $this->view('gps.form', [
            'device' => $device,
            'birds' => Bird::forUser(Auth::id()),
        ]);
    }

    public function update(string $id): void
    {
        $device = GpsDevice::findOwned((int) $id, Auth::id());
        if (!$device) {
            App::notFound();
        }
        $d = $this->validate(['name' => 'required']);
        GpsDevice::update((int) $id, [
            'name' => $d['name'],
            'model' => trim($_POST['model'] ?? '') ?: null,
            'bird_id' => ($_POST['bird_id'] ?? '') ? (int) $_POST['bird_id'] : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);
        Session::flash('success', 'Устройството е обновено.');
        $this->redirect('/dashboard/gps/' . $id);
    }

    public function destroy(string $id): void
    {
        GpsDevice::delete((int) $id, Auth::id());
        Session::flash('success', 'Устройството е премахнато.');
        $this->redirect('/dashboard/gps');
    }

    public function regenerateToken(string $id): void
    {
        $device = GpsDevice::findOwned((int) $id, Auth::id());
        if (!$device) {
            App::notFound();
        }
        $token = bin2hex(random_bytes(32));
        GpsDevice::update((int) $id, ['api_token' => $token]);
        Session::flash('success', 'Нов API токен е генериран.');
        $this->redirect('/dashboard/gps/' . $id);
    }
}
