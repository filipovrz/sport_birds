<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Loft
{
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT l.*, (SELECT COUNT(*) FROM birds b WHERE b.loft_id = l.id) AS bird_count
             FROM lofts l WHERE l.user_id = ? ORDER BY l.name',
            [$userId]
        );
    }

    public static function findOwned(int $id, int $userId): ?array
    {
        return Database::fetch('SELECT * FROM lofts WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function countForUser(int $userId): int
    {
        $row = Database::fetch('SELECT COUNT(*) AS c FROM lofts WHERE user_id = ?', [$userId]);
        return (int) ($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        return Database::insert('lofts', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('lofts', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $userId): void
    {
        Database::query('DELETE FROM lofts WHERE id = ? AND user_id = ?', [$id, $userId]);
    }
}
