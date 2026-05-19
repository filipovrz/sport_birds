<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bird;
use App\Models\Loft;
use App\Services\SubscriptionService;
use App\Services\UploadService;

final class BirdController extends Controller
{
    public function index(): void
    {
        $this->view('birds.index', [
            'birds' => Bird::forUser(Auth::id()),
        ]);
    }

    public function create(): void
    {
        if (!SubscriptionService::hasFeature('birds')) {
            Session::flash('error', 'Нужен е платен план.');
            $this->redirect('/dashboard/subscription');
        }
        if (!SubscriptionService::canAddBird(Auth::id())) {
            Session::flash('error', 'Достигнахте лимита на птици за вашия план.');
            $this->redirect('/dashboard/subscription');
        }
        $this->view('birds.form', [
            'bird' => null,
            'lofts' => Loft::forUser(Auth::id()),
            'parents' => Bird::parentsOptions(Auth::id()),
        ]);
    }

    public function store(): void
    {
        if (!SubscriptionService::canAddBird(Auth::id())) {
            Session::flash('error', 'Лимит на птици.');
            $this->redirect('/dashboard/subscription');
        }
        try {
            $data = $this->birdData();
            $this->validateParents($data);
            $data['user_id'] = Auth::id();
            if (!empty($_FILES['photo']['name'])) {
                $data['photo_path'] = UploadService::storeBirdPhoto($_FILES['photo'], Auth::id());
            }
            Bird::create($data);
            Session::flash('success', 'Птицата е регистрирана.');
            $this->redirect('/dashboard/birds');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function show(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            App::notFound();
        }
        $this->view('birds.show', ['bird' => $bird]);
    }

    public function edit(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            App::notFound();
        }
        $this->view('birds.form', [
            'bird' => $bird,
            'lofts' => Loft::forUser(Auth::id()),
            'parents' => Bird::parentsOptions(Auth::id(), (int) $id),
        ]);
    }

    public function update(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            App::notFound();
        }
        try {
            $data = $this->birdData();
            $this->validateParents($data, (int) $id);
            if (!empty($_FILES['photo']['name'])) {
                UploadService::delete($bird['photo_path']);
                $data['photo_path'] = UploadService::storeBirdPhoto($_FILES['photo'], Auth::id());
            }
            if (isset($_POST['remove_photo'])) {
                UploadService::delete($bird['photo_path']);
                $data['photo_path'] = null;
            }
            Bird::update((int) $id, $data);
            Session::flash('success', 'Записът е обновен.');
            $this->redirect('/dashboard/birds/' . $id);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function destroy(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if ($bird) {
            UploadService::delete($bird['photo_path']);
        }
        Bird::delete((int) $id, Auth::id());
        Session::flash('success', 'Птицата е изтрита.');
        $this->redirect('/dashboard/birds');
    }

    /** @param array<string, mixed> $data */
    private function validateParents(array $data, int $excludeBirdId = 0): void
    {
        $userId = Auth::id();
        $fatherId = !empty($data['father_id']) ? (int) $data['father_id'] : 0;
        $motherId = !empty($data['mother_id']) ? (int) $data['mother_id'] : 0;

        if ($fatherId) {
            if ($excludeBirdId && $fatherId === $excludeBirdId) {
                throw new \RuntimeException('Птицата не може да е баща на самата себе си.');
            }
            $father = Bird::findOwned($fatherId, $userId);
            if (!$father || ($father['sex'] ?? '') !== 'male') {
                throw new \RuntimeException('Бащата трябва да е птица, маркирана като мъжка.');
            }
        }
        if ($motherId) {
            if ($excludeBirdId && $motherId === $excludeBirdId) {
                throw new \RuntimeException('Птицата не може да е майка на самата себе си.');
            }
            $mother = Bird::findOwned($motherId, $userId);
            if (!$mother || ($mother['sex'] ?? '') !== 'female') {
                throw new \RuntimeException('Майката трябва да е птица, маркирана като женска.');
            }
        }
        if ($fatherId && $motherId && $fatherId === $motherId) {
            throw new \RuntimeException('Бащата и майката не могат да са една и съща птица.');
        }
    }

    /** @return array<string, mixed> */
    private function birdData(): array
    {
        $d = $this->validate(['ring_number' => 'required']);
        return [
            'loft_id' => ($_POST['loft_id'] ?? '') ? (int) $_POST['loft_id'] : null,
            'ring_number' => $d['ring_number'],
            'name' => trim($_POST['name'] ?? '') ?: null,
            'species' => $_POST['species'] ?? 'racing_pigeon',
            'sex' => $_POST['sex'] ?? 'unknown',
            'color' => trim($_POST['color'] ?? '') ?: null,
            'strain' => trim($_POST['strain'] ?? '') ?: null,
            'birth_date' => ($_POST['birth_date'] ?? '') ?: null,
            'status' => $_POST['status'] ?? 'active',
            'father_id' => ($_POST['father_id'] ?? '') ? (int) $_POST['father_id'] : null,
            'mother_id' => ($_POST['mother_id'] ?? '') ? (int) $_POST['mother_id'] : null,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'is_public_pedigree' => isset($_POST['is_public_pedigree']) ? 1 : 0,
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
        ];
    }
}
