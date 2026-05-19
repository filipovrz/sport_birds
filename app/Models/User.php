<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function findByVerificationToken(string $token): ?array
    {
        return Database::fetch(
            'SELECT * FROM users WHERE email_verification_token = ? AND email_verified_at IS NULL LIMIT 1',
            [$token]
        );
    }

    public static function markEmailVerified(int $id): void
    {
        Database::query(
            'UPDATE users SET email_verified_at = NOW(), email_verification_token = NULL, updated_at = NOW() WHERE id = ?',
            [$id]
        );
    }

    public static function create(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return Database::insert('users', $data);
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        Database::update('users', $data, 'id = ?', [$id]);
    }

    public static function allForAdmin(): array
    {
        return Database::fetchAll(
            'SELECT u.*, p.name AS plan_name FROM users u
             LEFT JOIN subscription_plans p ON p.id = u.subscription_plan_id
             ORDER BY u.created_at DESC'
        );
    }
}
