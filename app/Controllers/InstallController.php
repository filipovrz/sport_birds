<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Session;
use PDO;
use PDOException;

final class InstallController extends Controller
{
    public function index(): void
    {
        $installed = App::isInstalled();
        $this->view('install.index', ['installed' => $installed], 'layouts.guest');
    }

    public function run(): void
    {
        if (App::isInstalled()) {
            $this->redirect('/login');
        }

        $host = $_POST['db_host'] ?? '127.0.0.1';
        $port = $_POST['db_port'] ?? '3306';
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['db_name'] ?? '');
        $user = $_POST['db_user'] ?? '';
        $pass = $_POST['db_pass'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        $adminPass = $_POST['admin_password'] ?? '';
        $adminName = $_POST['admin_name'] ?? 'Super Admin';
        $appEnv = ($_POST['app_env'] ?? 'production') === 'local' ? 'local' : 'production';
        $appDebug = isset($_POST['app_debug']) ? 'true' : 'false';

        if (!$name || !$user || !$adminEmail || strlen($adminPass) < 8) {
            Session::flash('error', 'Попълнете всички задължителни полета (парола мин. 8 символа).');
            $this->back();
        }

        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$name}`");

            $schema = file_get_contents(BASE_PATH . '/database/schema.sql');
            $this->runSchema($pdo, $schema);

            $hash = password_hash($adminPass, PASSWORD_DEFAULT);
            $pdo->prepare(
                'INSERT INTO users (email, password, name, role, user_type, bird_specialties, subscription_plan_id, subscription_expires_at)
                 VALUES (?, ?, ?, \'super_admin\', \'owner,competitor,breeder\', \'racing_pigeon,sport_pigeon,gamecock,other_sport_bird\', 3, DATE_ADD(NOW(), INTERVAL 10 YEAR))
                 ON DUPLICATE KEY UPDATE password = VALUES(password), role = \'super_admin\''
            )->execute([$adminEmail, $hash, $adminName]);

            $env = "APP_ENV={$appEnv}\nAPP_DEBUG={$appDebug}\nAPP_URL=" . ($_POST['app_url'] ?? '') . "\n";
            $env .= "DB_HOST={$host}\nDB_PORT={$port}\nDB_DATABASE={$name}\nDB_USERNAME={$user}\nDB_PASSWORD={$pass}\n";
            file_put_contents(BASE_PATH . '/.env', $env);

            $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('app_installed', '1') ON DUPLICATE KEY UPDATE `value` = '1'")->execute();
            $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('maintenance_mode', '0') ON DUPLICATE KEY UPDATE `value` = '0'")->execute();

            $this->view('install.success', ['admin_email' => $adminEmail], 'layouts.guest');
        } catch (PDOException $e) {
            Session::flash('error', 'Грешка при инсталация: ' . $e->getMessage());
            $this->back();
        }
    }

    private function runSchema(PDO $pdo, string $schema): void
    {
        $schema = preg_replace('/--.*$/m', '', $schema);
        foreach (preg_split('/;\s*(?:\r\n|\n|\r)/', $schema) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') {
                continue;
            }
            $pdo->exec($stmt);
        }
    }
}
