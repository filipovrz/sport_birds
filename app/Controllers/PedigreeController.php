<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Bird;
use App\Services\PedigreeService;
use App\Services\SubscriptionService;

final class PedigreeController extends Controller
{
    public function show(string $id): void
    {
        $bird = Bird::findOwned((int) $id, Auth::id());
        if (!$bird) {
            http_response_code(404);
            exit;
        }
        $generations = SubscriptionService::hasFeature('pedigree_export') ? 5 : 3;
        $this->view('pedigree.show', [
            'bird' => $bird,
            'tree' => PedigreeService::buildTree((int) $id, Auth::id(), $generations),
            'inbreeding' => PedigreeService::inbreedingCoefficient((int) $id, Auth::id()),
        ]);
    }
}
