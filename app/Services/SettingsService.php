<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class SettingsService
{
    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $row = Database::fetch('SELECT `value` FROM settings WHERE `key` = ?', [$key]);
            return $row['value'] ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set(string $key, string $value): void
    {
        Database::query(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value]
        );
    }

    public static function announcementPublishFeeEur(): float
    {
        return max(0, (float) self::get('announcement_publish_fee_eur', '10'));
    }

    public static function eventPublishFeeEur(): float
    {
        return max(0, (float) self::get('event_publish_fee_eur', '5'));
    }

    public static function paymentInstructions(): string
    {
        return PaymentMethodsService::instructionsForForms();
    }
}
