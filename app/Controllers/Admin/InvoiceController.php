<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class InvoiceController extends Controller
{
    public function index(): void
    {
        $invoices = Database::fetchAll(
            'SELECT i.*, u.name AS user_name, u.email AS user_email
             FROM invoices i
             JOIN users u ON u.id = i.user_id
             ORDER BY i.issue_date DESC, i.id DESC
             LIMIT 500'
        );
        $this->view('admin.invoices.index', ['invoices' => $invoices], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $invoice = $this->findInvoice((int) $id);
        if (!$invoice) {
            Session::flash('error', 'Фактурата не е намерена.');
            $this->redirect('/admin/invoices');
        }
        $config = require BASE_PATH . '/config/app.php';
        $this->view('admin.invoices.show', [
            'invoice' => $invoice,
            'config' => $config,
        ], 'layouts.admin');
    }

    public function print(string $id): void
    {
        $invoice = $this->findInvoice((int) $id);
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

    /** @return array<string, mixed>|null */
    private function findInvoice(int $id): ?array
    {
        return Database::fetch(
            'SELECT i.*, u.name AS user_name, u.email AS user_email
             FROM invoices i
             JOIN users u ON u.id = i.user_id
             WHERE i.id = ?',
            [$id]
        );
    }
}
