<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::get(self::KEY)) {
            Session::set(self::KEY, bin2hex(random_bytes(32)));
        }
        return Session::get(self::KEY);
    }

    public static function field(): string
    {
        $t = htmlspecialchars(self::token());
        return '<input type="hidden" name="_token" value="' . $t . '">';
    }

    public static function verify(): bool
    {
        $sent = $_POST['_token'] ?? '';
        $stored = Session::get(self::KEY, '');
        return $sent !== '' && hash_equals((string) $stored, $sent);
    }
}
