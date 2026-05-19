<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;

final class AdminPermissionService
{
    /** @var array<string, string> */
    public const PERMISSIONS = [
        'settings' => 'Настройки и футър',
        'users' => 'Потребители',
        'plans' => 'Планове',
        'subscriptions' => 'Абонаменти',
        'announcements' => 'Обяви — състезания',
        'events' => 'Обяви — събития',
    ];

    public static function can(string $permission): bool
    {
        $user = Auth::user();
        if (!$user || !Auth::isAdmin()) {
            return false;
        }
        if (Auth::isSuperAdmin()) {
            return true;
        }
        if (($user['role'] ?? '') !== 'admin') {
            return false;
        }

        $granted = self::permissionsForUser($user);
        if ($granted === null) {
            return true;
        }

        return in_array($permission, $granted, true);
    }

    /** @return list<string>|null null = пълен достъп (наследени админи) */
    public static function permissionsForUser(array $user): ?array
    {
        $raw = $user['admin_permissions'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                return [];
            }
            return array_values(array_intersect($decoded, array_keys(self::PERMISSIONS)));
        }

        return is_array($raw) ? array_values(array_intersect($raw, array_keys(self::PERMISSIONS))) : [];
    }

    /** @param list<string> $permissions */
    public static function saveForUser(int $userId, array $permissions): void
    {
        $clean = array_values(array_unique(array_intersect($permissions, array_keys(self::PERMISSIONS))));
        \App\Models\User::update($userId, [
            'admin_permissions' => json_encode($clean, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public static function permissionForAdminPath(string $path): ?string
    {
        if (str_starts_with($path, '/admin/footer') || str_starts_with($path, '/admin/settings')) {
            return 'settings';
        }
        if (str_starts_with($path, '/admin/users')) {
            return 'users';
        }
        if (str_starts_with($path, '/admin/plans')) {
            return 'plans';
        }
        if (str_starts_with($path, '/admin/subscriptions')) {
            return 'subscriptions';
        }
        if (str_starts_with($path, '/admin/announcement-payments')
            || str_starts_with($path, '/admin/competition-archive')) {
            return 'announcements';
        }
        if (str_starts_with($path, '/admin/event-payments')
            || str_starts_with($path, '/admin/event-archive')) {
            return 'events';
        }
        if ($path === '/admin' || $path === '/admin/') {
            return null;
        }
        if (str_starts_with($path, '/admin/health-reminders')) {
            return 'settings';
        }

        return null;
    }
}
