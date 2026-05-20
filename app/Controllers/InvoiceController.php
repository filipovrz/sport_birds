<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Services\InvoiceService;

final class InvoiceController extends Controller
{
    public function index(): void
    {
        $this->view('invoices.index', [
            'invoices' => InvoiceService::listForUser(Auth::id()),
        ]);
    }

    public function show(string $id): void
    {
        $invoice = InvoiceService::findByIdForUser((int) $id, Auth::id());
        if (!$invoice) {
            Session::flash('error', 'Фактурата не е намерена.');
            $this->redirect('/dashboard/invoices');
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('invoices.show', [
            'invoice' => $invoice,
            'config' => $config,
        ]);
    }

    public function print(string $id): void
    {
        $invoice = InvoiceService::findByIdForUser((int) $id, Auth::id());
        if (!$invoice) {
            http_response_code(404);
            echo 'Фактурата не е намерена.';
            exit;
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('invoices.print', [
            'invoice' => $invoice,
            'config' => $config,
        ], null);
    }
}
