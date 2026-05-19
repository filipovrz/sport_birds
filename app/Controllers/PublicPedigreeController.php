<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Bird;
use App\Services\PedigreeService;

final class PublicPedigreeController extends Controller
{
    public function show(string $id): void
    {
        $bird = Bird::find((int) $id);
        if (!$bird || !$bird['is_public_pedigree']) {
            App::notFound();
        }
        $tree = PedigreeService::buildTree((int) $id, (int) $bird['user_id'], 4);
        $owner = Database::fetch('SELECT name, club_name FROM users WHERE id = ?', [$bird['user_id']]);
        $this->view('pedigree.public', [
            'bird' => $bird,
            'tree' => $tree,
            'owner' => $owner,
        ], 'layouts.guest');
    }
}
