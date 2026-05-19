<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\GpsDevice;

/** API за GPS устройства — без CSRF, автентикация с api_token */
final class GpsApiController
{
    public function track(): void
    {
        $input = $this->parseInput();
        $token = $input['token'] ?? $_SERVER['HTTP_X_GPS_TOKEN'] ?? '';
        if ($token === '') {
            View::json(['error' => 'Missing token'], 401);
        }

        $device = GpsDevice::findByToken($token);
        if (!$device) {
            View::json(['error' => 'Invalid token'], 403);
        }

        $lat = (float) ($input['latitude'] ?? $input['lat'] ?? 0);
        $lng = (float) ($input['longitude'] ?? $input['lng'] ?? $input['lon'] ?? 0);
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            View::json(['error' => 'Invalid coordinates'], 400);
        }

        GpsDevice::recordPosition((int) $device['id'], [
            'latitude' => $lat,
            'longitude' => $lng,
            'altitude' => isset($input['altitude']) ? (float) $input['altitude'] : null,
            'speed_kmh' => isset($input['speed_kmh']) ? (float) $input['speed_kmh'] : null,
            'battery_pct' => isset($input['battery']) ? (int) $input['battery'] : null,
            'recorded_at' => $input['recorded_at'] ?? date('Y-m-d H:i:s'),
        ]);

        View::json(['ok' => true, 'device_id' => (int) $device['id']]);
    }

    /** @return array<string, mixed> */
    private function parseInput(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw && str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                return $json;
            }
        }
        return array_merge($_GET, $_POST);
    }
}
