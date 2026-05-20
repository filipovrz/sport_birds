<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PaymentMethodsService;
use App\Services\SettingsService;

final class PaymentMethodsController extends Controller
{
    public function index(): void
    {
        $this->view('payment.methods', [
            'methods' => PaymentMethodsService::catalog(false),
            'bankInstructions' => trim(SettingsService::get('payment_instructions', '') ?? ''),
        ], 'layouts.guest');
    }

    public function show(string $slug): void
    {
        $method = PaymentMethodsService::find($slug);
        if ($method === null) {
            http_response_code(404);
            echo 'Начинът на плащане не е наличен.';
            exit;
        }
        $bankInstructions = trim(SettingsService::get('payment_instructions', '') ?? '');

        $this->view('payment.method_show', [
            'method' => $method,
            'bankInstructions' => $bankInstructions,
            'continueUrl' => PaymentMethodsService::continueUrl($slug),
        ], 'layouts.guest');
    }
}
