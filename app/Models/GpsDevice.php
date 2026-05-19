<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class GpsDevice
{
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT d.*, b.ring_number, b.name AS bird_name FROM gps_devices d
             LEFT JOIN birds b ON b.id = d.bird_id
             WHERE d.user_id = ? ORDER BY d.name',
            [$userId]
        );
    }

    public static function findOwned(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT d.*, b.ring_number FROM gps_devices d
             LEFT JOIN birds b ON b.id = d.bird_id
             WHERE d.id = ? AND d.user_id = ?',
            [$id, $userId]
        );
    }

    public static function findByToken(string $token): ?array
    {
        return Database::fetch('SELECT * FROM gps_devices WHERE api_token = ? AND is_active = 1', [$token]);
    }

    public static function create(array $data): int
    {
        if (empty($data['api_token'])) {
            $data['api_token'] = bin2hex(random_bytes(32));
        }
        return Database::insert('gps_devices', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('gps_devices', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $userId): void
    {
        Database::query('DELETE FROM gps_devices WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function recordPosition(int $deviceId, array $pos): void
    {
        Database::insert('gps_positions', [
            'device_id' => $deviceId,
            'latitude' => $pos['latitude'],
            'longitude' => $pos['longitude'],
            'altitude' => $pos['altitude'] ?? null,
            'speed_kmh' => $pos['speed_kmh'] ?? null,
            'battery_pct' => $pos['battery_pct'] ?? null,
            'recorded_at' => $pos['recorded_at'] ?? date('Y-m-d H:i:s'),
        ]);
        Database::update('gps_devices', [
            'last_latitude' => $pos['latitude'],
            'last_longitude' => $pos['longitude'],
            'last_altitude' => $pos['altitude'] ?? null,
            'last_speed_kmh' => $pos['speed_kmh'] ?? null,
            'last_battery_pct' => $pos['battery_pct'] ?? null,
            'last_seen_at' => $pos['recorded_at'] ?? date('Y-m-d H:i:s'),
        ], 'id = ?', [$deviceId]);
    }

    /** @return list<array> */
    public static function activeWithPosition(int $userId): array
    {
        return Database::fetchAll(
            'SELECT d.*, b.ring_number, b.name AS bird_name FROM gps_devices d
             LEFT JOIN birds b ON b.id = d.bird_id
             WHERE d.user_id = ? AND d.is_active = 1 AND d.last_latitude IS NOT NULL',
            [$userId]
        );
    }

    /** @return list<array> */
    public static function trackHistory(int $deviceId, int $userId, int $hours = 48): array
    {
        $device = self::findOwned($deviceId, $userId);
        if (!$device) {
            return [];
        }
        return Database::fetchAll(
            'SELECT * FROM gps_positions WHERE device_id = ? AND recorded_at >= DATE_SUB(NOW(), INTERVAL ? HOUR) ORDER BY recorded_at',
            [$deviceId, $hours]
        );
    }
}
