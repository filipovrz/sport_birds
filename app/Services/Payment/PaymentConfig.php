<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\SettingsService;

final class PaymentConfig
{
    /** @return array<string, mixed> */
    public static function all(): array
    {
        $file = require BASE_PATH . '/config/payments.php';
        $overrides = [
            'stripe' => [
                'secret_key' => SettingsService::get('stripe_secret_key', '') ?: $file['stripe']['secret_key'],
                'webhook_secret' => SettingsService::get('stripe_webhook_secret', '') ?: $file['stripe']['webhook_secret'],
                'enabled' => self::boolSetting('stripe_enabled', $file['stripe']['enabled']),
            ],
            'epay' => [
                'min' => SettingsService::get('epay_min', '') ?: $file['epay']['min'],
                'secret' => SettingsService::get('epay_secret', '') ?: $file['epay']['secret'],
                'url' => SettingsService::get('epay_url', '') ?: $file['epay']['url'],
                'enabled' => self::boolSetting('epay_enabled', $file['epay']['enabled']),
            ],
            'paypal' => [
                'client_id' => SettingsService::get('paypal_client_id', '') ?: $file['paypal']['client_id'],
                'secret' => SettingsService::get('paypal_secret', '') ?: $file['paypal']['secret'],
                'mode' => SettingsService::get('paypal_mode', '') ?: $file['paypal']['mode'],
                'enabled' => self::boolSetting('paypal_enabled', $file['paypal']['enabled']),
            ],
            'revolut' => [
                'api_secret' => SettingsService::get('revolut_api_secret', '') ?: $file['revolut']['api_secret'],
                'mode' => SettingsService::get('revolut_mode', '') ?: $file['revolut']['mode'],
                'enabled' => self::boolSetting('revolut_enabled', $file['revolut']['enabled']),
            ],
            'bank' => ['enabled' => true],
            'eur_bgn_rate' => (float) (SettingsService::get('payment_eur_bgn_rate', '') ?: (string) $file['eur_bgn_rate']),
        ];

        return array_replace_recursive($file, $overrides);
    }

    /** @return array<string, mixed> */
    public static function gateway(string $slug): array
    {
        $all = self::all();

        return $all[$slug] ?? [];
    }

    public static function eurToBgn(float $eur): float
    {
        return round($eur * self::all()['eur_bgn_rate'], 2);
    }

    private static function boolSetting(string $key, bool $default): bool
    {
        $v = SettingsService::get($key, '');
        if ($v === null || $v === '') {
            return $default;
        }

        return in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true);
    }
}
