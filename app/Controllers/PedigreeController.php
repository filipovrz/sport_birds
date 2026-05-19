<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bird;
use App\Services\PedigreeService;
use App\Services\SubscriptionService;

final class PedigreeController extends Controller
{
    public function show(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            App::notFound();
        }
        $generations = SubscriptionService::hasFeature('pedigree_export') ? 5 : 3;
        $this->view('pedigree.show', [
            'bird' => $bird,
            'tree' => PedigreeService::buildTree((int) $id, Auth::id(), $generations),
            'inbreeding' => PedigreeService::inbreedingCoefficient((int) $id, Auth::id()),
            'canExport' => SubscriptionService::hasFeature('pedigree_export'),
        ]);
    }

    public function print(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            App::notFound();
        }
        if (!SubscriptionService::hasFeature('pedigree_export')) {
            Session::flash('error', 'PDF/печат на родословие изисква Pro план.');
            $this->redirect('/dashboard/birds/' . $id . '/pedigree');
        }
        $this->view('pedigree.print', [
            'bird' => $bird,
            'tree' => PedigreeService::buildTree((int) $id, Auth::id(), 5),
            'inbreeding' => PedigreeService::inbreedingCoefficient((int) $id, Auth::id()),
        ], null);
    }
}
