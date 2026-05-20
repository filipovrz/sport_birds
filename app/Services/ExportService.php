<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Bird;
use App\Models\Loft;

final class ExportService
{
    public static function canExport(): bool
    {
        return Auth::hasPremium() && SubscriptionService::hasFeature('analytics');
    }

    /** @param list<array<string, mixed>> $rows @param list<string> $headers */
    public static function csvResponse(string $filename, array $headers, array $rows): never
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers, ';');
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $h) {
                $line[] = $row[$h] ?? '';
            }
            fputcsv($out, $line, ';');
        }
        fclose($out);
        exit;
    }

    public static function birdsCsv(int $userId): never
    {
        $birds = Bird::forUser($userId);
        $rows = [];
        foreach ($birds as $b) {
            $rows[] = [
                'ring_number' => $b['ring_number'] ?? '',
                'name' => $b['name'] ?? '',
                'species' => species_label($b['species'] ?? ''),
                'sex' => $b['sex'] ?? '',
                'status' => status_label($b['status'] ?? ''),
                'loft' => $b['loft_name'] ?? '',
                'line' => $b['line_name'] ?? '',
            ];
        }
        self::csvResponse('birds-' . date('Y-m-d') . '.csv', [
            'ring_number', 'name', 'species', 'sex', 'status', 'loft', 'line',
        ], $rows);
    }

    public static function loftsCsv(int $userId): never
    {
        $rows = [];
        foreach (Loft::forUser($userId) as $l) {
            $rows[] = [
                'name' => $l['name'] ?? '',
                'location' => $l['location'] ?? '',
                'latitude' => $l['latitude'] ?? '',
                'longitude' => $l['longitude'] ?? '',
                'capacity' => $l['capacity'] ?? '',
            ];
        }
        self::csvResponse('lofts-' . date('Y-m-d') . '.csv', [
            'name', 'location', 'latitude', 'longitude', 'capacity',
        ], $rows);
    }

    public static function competitionsCsv(int $userId): never
    {
        $comps = Database::fetchAll(
            'SELECT name, event_date, competition_type, location, notes FROM competitions WHERE user_id = ? ORDER BY event_date DESC',
            [$userId]
        );
        $rows = [];
        foreach ($comps as $c) {
            $rows[] = [
                'name' => $c['name'] ?? '',
                'event_date' => $c['event_date'] ?? '',
                'type' => competition_type_label($c['competition_type'] ?? ''),
                'location' => $c['location'] ?? '',
                'notes' => $c['notes'] ?? '',
            ];
        }
        self::csvResponse('competitions-' . date('Y-m-d') . '.csv', [
            'name', 'event_date', 'type', 'location', 'notes',
        ], $rows);
    }
}
