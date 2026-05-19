<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bird;

final class PedigreeService
{
    /** @return array<string, mixed> */
    public static function buildTree(int $birdId, int $userId, int $generations = 4): array
    {
        $bird = Bird::findOwned($birdId, $userId);
        if (!$bird) {
            return [];
        }
        return self::node($bird, $userId, $generations);
    }

    /** @return array<string, mixed> */
    private static function node(array $bird, int $userId, int depth): array
    {
        $data = [
            'id' => $bird['id'],
            'ring_number' => $bird['ring_number'],
            'name' => $bird['name'],
            'sex' => $bird['sex'],
            'strain' => $bird['strain'],
            'father' => null,
            'mother' => null,
        ];
        if ($depth <= 0) {
            return $data;
        }
        if ($bird['father_id']) {
            $father = Bird::findOwned((int) $bird['father_id'], $userId);
            if ($father) {
                $data['father'] = self::node($father, $userId, $depth - 1);
            }
        }
        if ($bird['mother_id']) {
            $mother = Bird::findOwned((int) $bird['mother_id'], $userId);
            if ($mother) {
                $data['mother'] = self::node($mother, $userId, $depth - 1);
            }
        }
        return $data;
    }

    public static function inbreedingCoefficient(int $birdId, int $userId): ?float
    {
        $ancestors = self::collectAncestors($birdId, $userId);
        $ids = array_column($ancestors, 'id');
        $counts = array_count_values($ids);
        $duplicates = array_filter($counts, fn ($c) => $c > 1);
        if (empty($duplicates)) {
            return 0.0;
        }
        return round(min(0.5, array_sum($duplicates) * 0.0125), 4);
    }

    /** @return list<array> */
    private static function collectAncestors(int $birdId, int $userId, int $depth = 5): array
    {
        $list = [];
        $bird = Bird::findOwned($birdId, $userId);
        if (!$bird || $depth <= 0) {
            return $list;
        }
        foreach (['father_id', 'mother_id'] as $side) {
            if ($bird[$side]) {
                $parent = Bird::findOwned((int) $bird[$side], $userId);
                if ($parent) {
                    $list[] = $parent;
                    $list = array_merge($list, self::collectAncestors($parent['id'], $userId, $depth - 1));
                }
            }
        }
        return $list;
    }
}
