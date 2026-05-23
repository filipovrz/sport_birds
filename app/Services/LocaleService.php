<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

final class LocaleService
{
    /** @var list<string> */
    public const SUPPORTED = ['bg', 'en'];

    private static string $locale = 'bg';

    /** @var array<string, mixed>|null */
    private static ?array $strings = null;

    public static function init(): void
    {
        $config = require BASE_PATH . '/config/app.php';
        $default = (string) ($config['locale'] ?? 'bg');
        $stored = Session::get('locale');
        $locale = is_string($stored) && in_array($stored, self::SUPPORTED, true) ? $stored : $default;
        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = 'bg';
        }
        self::$locale = $locale;
        self::$strings = null;
    }

    public static function set(string $locale): void
    {
        if (!in_array($locale, self::SUPPORTED, true)) {
            return;
        }
        Session::set('locale', $locale);
        self::$locale = $locale;
        self::$strings = null;
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    /** @param array<string, string|int|float> $replace */
    public static function translate(string $key, array $replace = []): string
    {
        if (self::$strings === null) {
            $path = BASE_PATH . '/resources/lang/' . self::$locale . '.php';
            if (!is_file($path)) {
                $path = BASE_PATH . '/resources/lang/bg.php';
            }
            self::$strings = require $path;
        }
        $value = self::dotGet(self::$strings, $key);
        if (!is_string($value)) {
            return $key;
        }
        foreach ($replace as $name => $replacement) {
            $value = str_replace(':' . $name, (string) $replacement, $value);
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    private static function dotGet(array $data, string $key): mixed
    {
        $current = $data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return null;
            }
            $current = $current[$part];
        }

        return $current;
    }
}
