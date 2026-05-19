<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }
        return User::find((int) $id);
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id ? (int) $id : null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user_id', $user['id']);
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
    }

    public static function logout(): void
    {
        Session::forget('user_id');
    }

    public static function check(): bool
    {
        return self::id() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'super_admin'], true);
    }

    public static function isSuperAdmin(): bool
    {
        $user = self::user();
        return $user && $user['role'] === 'super_admin';
    }

    public static function hasPremium(): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        if (in_array($user['role'], ['admin', 'super_admin'], true)) {
            return true;
        }
        if (!$user['subscription_expires_at']) {
            return false;
        }
        return strtotime($user['subscription_expires_at']) >= time();
    }
}
