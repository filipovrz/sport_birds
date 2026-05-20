<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Payment\PaymentGatewayRegistry;

/**
 * Каталог начини на плащане — футър, страница /payment-methods, форми.
 */
final class PaymentMethodsService
{
    /** @return list<array{slug: string, label: string, timing: string, automatic: bool, icon: string, active: bool}> */
    public static function catalog(bool $onlyActive = false): array
    {
        $items = [
            [
                'slug' => 'bank',
                'label' => 'Банков превод',
                'timing' => 'До 1 работен ден',
                'automatic' => false,
                'icon' => 'bank',
            ],
            [
                'slug' => 'stripe',
                'label' => 'Visa / Mastercard',
                'timing' => 'Веднага',
                'automatic' => true,
                'icon' => 'card',
            ],
            [
                'slug' => 'epay',
                'label' => 'ePay.bg',
                'timing' => 'Веднага',
                'automatic' => true,
                'icon' => 'epay',
            ],
            [
                'slug' => 'paypal',
                'label' => 'PayPal',
                'timing' => 'Веднага',
                'automatic' => true,
                'icon' => 'paypal',
            ],
            [
                'slug' => 'revolut',
                'label' => 'Revolut Pay',
                'timing' => 'Веднага',
                'automatic' => true,
                'icon' => 'revolut',
            ],
        ];

        $out = [];
        foreach ($items as $item) {
            $item['active'] = $item['slug'] === 'bank' || PaymentGatewayRegistry::get($item['slug']) !== null;
            if (!$onlyActive || $item['active']) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /** @return array{slug: string, label: string, timing: string, automatic: bool, icon: string, active: bool}|null */
    public static function find(string $slug): ?array
    {
        foreach (self::catalog(false) as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    public static function methodUrl(string $slug): string
    {
        return '/payment-methods/' . rawurlencode($slug);
    }

    public static function methodsPageUrl(): string
    {
        $cfg = require BASE_PATH . '/config/app.php';

        return rtrim((string) ($cfg['url'] ?? ''), '/') . '/payment-methods';
    }

    public static function checkoutUrl(string $slug): string
    {
        return '/payment/checkout/' . rawurlencode($slug);
    }

    public static function continueUrl(string $slug): string
    {
        if (\App\Core\Auth::check()) {
            return self::checkoutUrl($slug);
        }

        return '/login?redirect=' . rawurlencode(self::methodUrl($slug));
    }

    /** Текст за форми — само банкови реквизити + линк. */
    public static function instructionsForForms(): string
    {
        $custom = trim(SettingsService::get('payment_instructions', '') ?? '');
        if ($custom !== '' && !self::looksLikeAccidentalPaste($custom)) {
            return $custom . "\n\nВсички методи: " . self::methodsPageUrl();
        }

        return 'Изберете начин при плащане. Подробности: ' . self::methodsPageUrl();
    }

    public static function looksLikeAccidentalPaste(string $text): bool
    {
        return (bool) preg_match('/стават\s*:/ui', $text)
            || str_contains($text, 'Начини на плащане стават')
            || str_contains($text, 'копирал');
    }

    /** @deprecated */
    public static function forFooter(): array
    {
        return [];
    }

    public static function footerNote(): string
    {
        return '';
    }
}
