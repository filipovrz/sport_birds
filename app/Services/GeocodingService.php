<?php

declare(strict_types=1);

namespace App\Services;

final class GeocodingService
{
    /** @var array<string, array{lat: float, lng: float}|null> */
    private static array $cache = [];

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function resolve(?string $location, mixed $latitude, mixed $longitude): ?array
    {
        $lat = self::toCoord($latitude);
        $lng = self::toCoord($longitude);
        if ($lat !== null && $lng !== null) {
            return ['lat' => $lat, 'lng' => $lng];
        }

        $loc = trim((string) $location);
        if ($loc === '') {
            return null;
        }

        return self::geocode($loc);
    }

    private static function toCoord(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $f = (float) $value;

        return $f !== 0.0 ? $f : null;
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private static function geocode(string $location): ?array
    {
        $key = mb_strtolower($location);
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $query = rawurlencode($location . ', Bulgaria');
        $url = 'https://nominatim.openstreetmap.org/search?q=' . $query . '&format=json&limit=1';
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 4,
                'header' => "User-Agent: BestSportByrds/2.1.4\r\n",
            ],
        ]);
        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            self::$cache[$key] = null;

            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || empty($data[0]['lat']) || empty($data[0]['lon'])) {
            self::$cache[$key] = null;

            return null;
        }

        $result = [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon'],
        ];
        self::$cache[$key] = $result;

        return $result;
    }
}
