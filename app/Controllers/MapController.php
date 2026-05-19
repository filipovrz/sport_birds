<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\GpsDevice;
use App\Models\Loft;
use App\Services\GeocodingService;
use App\Services\SubscriptionService;
use App\Core\Database;

final class MapController extends Controller
{
    public function index(): void
    {
        if (!SubscriptionService::hasFeature('map')) {
            Session::flash('error', 'Картата изисква платен план.');
            $this->redirect('/dashboard/subscription');
        }
        $uid = Auth::id();
        $markers = [];

        foreach (Loft::forUser($uid) as $loft) {
            if ($loft['latitude'] && $loft['longitude']) {
                $markers[] = [
                    'type' => 'loft',
                    'lat' => (float) $loft['latitude'],
                    'lng' => (float) $loft['longitude'],
                    'title' => $loft['name'],
                    'desc' => $loft['location'] ?? 'Гълъбарник',
                ];
            }
        }

        foreach (GpsDevice::activeWithPosition($uid) as $d) {
            $markers[] = [
                'type' => 'gps',
                'lat' => (float) $d['last_latitude'],
                'lng' => (float) $d['last_longitude'],
                'title' => $d['name'],
                'desc' => ($d['ring_number'] ?? 'Без птица') . ' · ' . ($d['last_seen_at'] ?? ''),
                'url' => '/dashboard/gps/' . $d['id'],
            ];
        }

        $announcements = Database::fetchAll(
            "SELECT id, title, latitude, longitude, release_latitude, release_longitude,
                    event_date, location
             FROM competition_announcements
             WHERE status = 'published'
             AND event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             ORDER BY event_date ASC
             LIMIT 50"
        );
        foreach ($announcements as $a) {
            $coords = GeocodingService::resolve(
                $a['location'] ?? null,
                $a['latitude'] ?? $a['release_latitude'] ?? null,
                $a['longitude'] ?? $a['release_longitude'] ?? null
            );
            if (!$coords) {
                continue;
            }
            $markers[] = [
                'type' => 'competition',
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'title' => $a['title'],
                'desc' => $a['event_date'] . ' — ' . ($a['location'] ?? ''),
                'url' => '/announcements/' . $a['id'],
            ];
        }

        $this->view('map.index', ['markers' => $markers]);
    }
}
