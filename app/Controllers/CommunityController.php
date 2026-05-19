<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Bird;

final class CommunityController extends Controller
{
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $like = $q !== '' ? '%' . $q . '%' : null;

        $usersSql = "SELECT u.id, u.name, u.city, u.club_name, u.user_type, u.bird_specialties,
            (SELECT COUNT(*) FROM birds b WHERE b.user_id = u.id AND b.is_public = 1) AS public_birds
            FROM users u WHERE u.is_public_profile = 1 AND u.is_active = 1";
        $usersParams = [];
        if ($like) {
            $usersSql .= ' AND (u.name LIKE ? OR u.city LIKE ? OR u.club_name LIKE ?)';
            $usersParams = [$like, $like, $like];
        }
        $usersSql .= ' ORDER BY u.name LIMIT 100';

        $birdsSql = "SELECT b.id, b.ring_number, b.name, b.species, b.sex, u.name AS owner_name, u.id AS owner_id
            FROM birds b JOIN users u ON u.id = b.user_id
            WHERE b.is_public = 1 AND u.is_active = 1";
        $birdsParams = [];
        if ($like) {
            $birdsSql .= ' AND (b.ring_number LIKE ? OR b.name LIKE ? OR u.name LIKE ?)';
            $birdsParams = [$like, $like, $like];
        }
        $birdsSql .= ' ORDER BY b.ring_number LIMIT 100';

        $loftsSql = "SELECT l.id, l.name, l.location, u.name AS owner_name, u.id AS owner_id
            FROM lofts l JOIN users u ON u.id = l.user_id
            WHERE l.is_public = 1 AND u.is_active = 1";
        $loftsParams = [];
        if ($like) {
            $loftsSql .= ' AND (l.name LIKE ? OR l.location LIKE ? OR u.name LIKE ?)';
            $loftsParams = [$like, $like, $like];
        }
        $loftsSql .= ' ORDER BY l.name LIMIT 100';

        $breedingSql = "SELECT bp.id, bp.season_year, m.ring_number AS male_ring, f.ring_number AS female_ring,
            u.name AS owner_name, u.id AS owner_id
            FROM breeding_pairs bp
            JOIN users u ON u.id = bp.user_id
            JOIN birds m ON m.id = bp.male_bird_id
            JOIN birds f ON f.id = bp.female_bird_id
            WHERE bp.is_public = 1 AND u.is_active = 1";
        $breedingParams = [];
        if ($like) {
            $breedingSql .= ' AND (m.ring_number LIKE ? OR f.ring_number LIKE ? OR u.name LIKE ?)';
            $breedingParams = [$like, $like, $like];
        }
        $breedingSql .= ' ORDER BY bp.season_year DESC LIMIT 100';

        $this->view('community.index', [
            'q' => $q,
            'users' => Database::fetchAll($usersSql, $usersParams),
            'birds' => Database::fetchAll($birdsSql, $birdsParams),
            'lofts' => Database::fetchAll($loftsSql, $loftsParams),
            'breeding' => Database::fetchAll($breedingSql, $breedingParams),
        ]);
    }

    public function user(string $id): void
    {
        $user = $this->publicUser((int) $id);
        if (!$user) {
            App::notFound();
        }
        $birds = Database::fetchAll(
            'SELECT id, ring_number, name, species, sex, status FROM birds WHERE user_id = ? AND is_public = 1 ORDER BY ring_number',
            [(int) $id]
        );
        $lofts = Database::fetchAll(
            'SELECT id, name, location, capacity FROM lofts WHERE user_id = ? AND is_public = 1 ORDER BY name',
            [(int) $id]
        );
        $breeding = Database::fetchAll(
            "SELECT bp.id, bp.season_year, m.ring_number AS male_ring, f.ring_number AS female_ring
             FROM breeding_pairs bp
             JOIN birds m ON m.id = bp.male_bird_id
             JOIN birds f ON f.id = bp.female_bird_id
             WHERE bp.user_id = ? AND bp.is_public = 1 ORDER BY bp.season_year DESC",
            [(int) $id]
        );
        $this->view('community.user', [
            'profile' => $user,
            'birds' => $birds,
            'lofts' => $lofts,
            'breeding' => $breeding,
            'isSelf' => Auth::id() === (int) $id,
        ]);
    }

    public function bird(string $id): void
    {
        $bird = Database::fetch(
            "SELECT b.*, u.name AS owner_name, u.id AS owner_id, u.club_name AS owner_club,
                    l.name AS loft_name
             FROM birds b
             JOIN users u ON u.id = b.user_id
             LEFT JOIN lofts l ON l.id = b.loft_id
             WHERE b.id = ? AND b.is_public = 1 AND u.is_active = 1",
            [(int) $id]
        );
        if (!$bird) {
            App::notFound();
        }
        $father = $bird['father_id'] ? Bird::find((int) $bird['father_id']) : null;
        $mother = $bird['mother_id'] ? Bird::find((int) $bird['mother_id']) : null;
        $this->view('community.bird', [
            'bird' => $bird,
            'father' => ($father && !empty($father['is_public'])) ? $father : null,
            'mother' => ($mother && !empty($mother['is_public'])) ? $mother : null,
            'isOwner' => Auth::id() === (int) $bird['user_id'],
        ]);
    }

    public function loft(string $id): void
    {
        $loft = Database::fetch(
            "SELECT l.*, u.name AS owner_name, u.id AS owner_id, u.club_name AS owner_club
             FROM lofts l
             JOIN users u ON u.id = l.user_id
             WHERE l.id = ? AND l.is_public = 1 AND u.is_active = 1",
            [(int) $id]
        );
        if (!$loft) {
            App::notFound();
        }
        $birds = Database::fetchAll(
            'SELECT id, ring_number, name, species, sex, status FROM birds WHERE loft_id = ? AND is_public = 1 ORDER BY ring_number',
            [(int) $id]
        );
        $this->view('community.loft', [
            'loft' => $loft,
            'birds' => $birds,
            'isOwner' => Auth::id() === (int) $loft['user_id'],
        ]);
    }

    public function breeding(string $id): void
    {
        $pair = Database::fetch(
            "SELECT bp.*, m.ring_number AS male_ring, m.id AS male_id, f.ring_number AS female_ring, f.id AS female_id,
                    u.name AS owner_name, u.id AS owner_id, u.club_name AS owner_club
             FROM breeding_pairs bp
             JOIN users u ON u.id = bp.user_id
             JOIN birds m ON m.id = bp.male_bird_id
             JOIN birds f ON f.id = bp.female_bird_id
             WHERE bp.id = ? AND bp.is_public = 1 AND u.is_active = 1",
            [(int) $id]
        );
        if (!$pair) {
            App::notFound();
        }
        $clutches = Database::fetchAll(
            'SELECT * FROM breeding_clutches WHERE breeding_pair_id = ? ORDER BY laid_at',
            [(int) $id]
        );
        $this->view('community.breeding', [
            'pair' => $pair,
            'clutches' => $clutches,
            'isOwner' => Auth::id() === (int) $pair['user_id'],
        ]);
    }

    /** @return array|null */
    private function publicUser(int $id): ?array
    {
        return Database::fetch(
            'SELECT id, name, city, country, club_name, user_type, bird_specialties, federation_id, created_at
             FROM users WHERE id = ? AND is_public_profile = 1 AND is_active = 1',
            [$id]
        );
    }
}
