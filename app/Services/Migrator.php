<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class Migrator
{
    /** @var array<string, string> version => sql file */
    private const PHASES = [
        '2.0.0' => 'phase2.sql',
        '2.1.0' => 'phase3.sql',
        '2.1.1' => 'phase3_1.sql',
        '2.1.2' => 'phase3_2.sql',
        '2.1.3' => 'phase3_3.sql',
        '2.1.4' => 'phase3_4.sql',
        '2.1.5' => 'phase3_5.sql',
        '2.1.6' => 'phase3_6.sql',
        '2.1.7' => 'phase3_7.sql',
        '2.2.0' => 'phase3_8.sql',
        '3.0.0' => 'phase4_payments.sql',
        '3.0.1' => 'phase4_1_footer_cleanup.sql',
    ];

    public static function currentVersion(): string
    {
        try {
            $row = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'app_version'");
            return $row['value'] ?? '1.0.0';
        } catch (\Throwable) {
            return '1.0.0';
        }
    }

    public static function runPending(): void
    {
        if (!is_file(BASE_PATH . '/.env')) {
            return;
        }
        $current = self::currentVersion();
        foreach (self::PHASES as $version => $file) {
            if (version_compare($current, $version, '>=')) {
                continue;
            }
            self::runFile(BASE_PATH . '/database/' . $file);
            Database::query(
                'INSERT INTO settings (`key`, `value`) VALUES ("app_version", ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$version]
            );
            $current = $version;
        }
    }

    private static function runFile(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        $pdo = Database::connection();
        $sql = preg_replace('/--.*$/m', '', file_get_contents($path));
        foreach (preg_split('/;\s*(?:\r\n|\n|\r)/', $sql) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt !== '') {
                $pdo->exec($stmt);
            }
        }
    }
}
