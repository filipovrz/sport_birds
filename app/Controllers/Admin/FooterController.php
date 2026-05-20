<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Services\AdminPermissionService;
use App\Services\FooterService;
use App\Services\LegalContentService;

final class FooterController extends Controller
{
    public function index(): void
    {
        if (!AdminPermissionService::can('settings')) {
            http_response_code(403);
            echo 'Нямате право за редакция на футъра.';
            exit;
        }
        $footer = FooterService::config();
        $legal = LegalContentService::allPages();
        $this->view('admin.footer', [
            'footer' => $footer,
            'legal' => $legal,
        ], 'layouts.admin');
    }

    public function update(): void
    {
        if (!AdminPermissionService::can('settings')) {
            http_response_code(403);
            echo 'Нямате право за редакция на футъра.';
            exit;
        }
        FooterService::saveFromPost($_POST);
        Session::flash('success', 'Футърът и правните страници са запазени.');
        $this->redirect('/admin/footer');
    }
}
