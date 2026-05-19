<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class InstallController extends Controller
{
    public function index(): void
    {
        $installed = false;
        try {
            $row = Database::fetch("SELECT `value` FROM settings WHERE `key` = 'app_installed'");
            $installed = $row && $row['value'] === '1';
        } catch (\Throwable) {
            // not installed yet
        }
        $this->view('install.index', ['installed' => $installed], 'layouts.guest');
    }

    public function run(): void
    {
        $host = $_POST['db_host'] ?? '127.0.0.1';
        $port = $_POST['db_port'] ?? '3306';
        $name = $_POST['db_name'] ?? '';
        $user = $_POST['db_user'] ?? '';
        $pass = $_POST['db_pass'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        $adminPass = $_POST['admin_password'] ?? '';
        $adminName = $_POST['admin_name'] ?? 'Super Admin';

        if (!$name || !$user || !$adminEmail || strlen($adminPass) < 8) {
            \App\Core\Session::flash('error', 'Попълнете всички задължителни полета (парола мин. 8 символа).');
            $this->back();
        }

        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$name}`");

        $schema = file_get_contents(BASE_PATH . '/database/schema.sql');
        if ($pdo->exec($schema) === false) {
            $offset = 0;
            foreach (preg_split('/;\s*\n/', $schema) as $stmt) {
                $stmt = trim($stmt);
                if ($stmt === '' || str_starts_with($stmt, '--')) {
                    continue;
                }
                $pdo->exec($stmt);
            }
        }

        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $pdo->prepare(
            "INSERT INTO users (email, password, name, role, user_type, bird_specialties, subscription_plan_id, subscription_expires_at)
             VALUES (?, ?, ?, 'super_admin', 'owner,competitor,breeder', 'racing_pigeon,sport_pigeon,gamecock,other_sport_bird', 3, DATE_ADD(NOW(), INTERVAL 10 YEAR))
             ON DUPLICATE KEY UPDATE role = 'super_admin'"
        )->execute([$adminEmail, $hash, $adminName]);

        $env = "APP_ENV=production\nAPP_DEBUG=false\nAPP_URL=" . ($_POST['app_url'] ?? '') . "\n";
        $env .= "DB_HOST={$host}\nDB_PORT={$port}\nDB_DATABASE={$name}\nDB_USERNAME={$user}\nDB_PASSWORD={$pass}\n";
        file_put_contents(BASE_PATH . '/.env', $env);

        $pdo->prepare("UPDATE settings SET `value` = '1' WHERE `key` = 'app_installed'")->execute();

        $this->view('install.success', ['admin_email' => $adminEmail], 'layouts.guest');
    }
}
