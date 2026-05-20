<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Services\LegalContentService;

final class LegalController extends Controller
{
    public function show(string $slug): void
    {
        if (!LegalContentService::slugExists($slug)) {
            App::notFound();
        }
        $content = LegalContentService::content($slug);
        if (trim($content) === '') {
            $content = 'Съдържанието все още не е публикувано. Администраторът може да го попълни от „Футър и политики“ в админ панела.';
        }
        $this->view('legal.show', [
            'title' => LegalContentService::title($slug),
            'content' => $content,
            'slug' => $slug,
        ], 'layouts.guest');
    }
}
