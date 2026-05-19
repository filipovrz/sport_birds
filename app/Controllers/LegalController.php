<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Services\FooterService;

final class LegalController extends Controller
{
    public function show(string $slug): void
    {
        $pages = FooterService::legalPages();
        if (!isset($pages[$slug])) {
            App::notFound();
        }
        $content = trim($pages[$slug]);
        if ($content === '') {
            $content = 'Съдържанието все още не е публикувано. Администраторът може да го попълни от „Футър и политики“ в админ панела.';
        }
        $this->view('legal.show', [
            'title' => FooterService::legalTitle($slug),
            'content' => $content,
            'slug' => $slug,
        ], 'layouts.guest');
    }
}
