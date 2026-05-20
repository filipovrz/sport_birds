<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Services\ExportService;

final class ExportController extends Controller
{
    public function birds(): void
    {
        $this->guard();
        ExportService::birdsCsv(Auth::id());
    }

    public function lofts(): void
    {
        $this->guard();
        ExportService::loftsCsv(Auth::id());
    }

    public function competitions(): void
    {
        $this->guard();
        ExportService::competitionsCsv(Auth::id());
    }

    private function guard(): void
    {
        if (!ExportService::canExport()) {
            Session::flash('error', 'CSV експортът е достъпен при план с аналитика (Стандарт и по-високи).');
            $this->redirect('/dashboard/subscription');
        }
    }
}
