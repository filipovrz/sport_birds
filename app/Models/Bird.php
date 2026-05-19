<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Bird
{
    public static function forUser(int $userId, ?int $loftId = null): array
    {
        $sql = 'SELECT b.*, l.name AS loft_name FROM birds b
                LEFT JOIN lofts l ON l.id = b.loft_id
                WHERE b.user_id = ?';
        $params = [$userId];
        if ($loftId) {
            $sql .= ' AND b.loft_id = ?';
            $params[] = $loftId;
        }
        $sql .= ' ORDER BY b.ring_number';
        return Database::fetchAll($sql, $params);
    }

    public static function findOwned(int $id, int $userId): ?array
    {
        return Database::fetch('SELECT * FROM birds WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM birds WHERE id = ?', [$id]);
    }

    public static function countForUser(int $userId): int
    {
        $row = Database::fetch('SELECT COUNT(*) AS c FROM birds WHERE user_id = ?', [$userId]);
        return (int) ($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        return Database::insert('birds', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('birds', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $userId): void
    {
        Database::query('DELETE FROM birds WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function parentsOptions(int $userId, ?int $excludeId = null): array
    {
        $sql = 'SELECT id, ring_number, name, sex FROM birds WHERE user_id = ? AND status != "deceased"';
        $params = [$userId];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY ring_number';
        return Database::fetchAll($sql, $params);
    }
}
