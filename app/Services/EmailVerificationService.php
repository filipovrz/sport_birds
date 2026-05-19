<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class EmailVerificationService
{
    public static function createToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function verificationUrl(string $token): string
    {
        $config = require BASE_PATH . '/config/app.php';
        $base = rtrim($_ENV['APP_URL'] ?? $config['url'] ?? 'http://localhost:8080', '/');

        return $base . '/verify-email?token=' . urlencode($token);
    }

    public static function sendVerificationEmail(array $user): bool
    {
        $token = $user['email_verification_token'] ?? '';
        if ($token === '') {
            return false;
        }
        $url = self::verificationUrl($token);
        $name = $user['name'] ?? '';
        $config = require BASE_PATH . '/config/app.php';
        $site = SettingsService::get('site_name') ?: ($config['name'] ?? 'Best Sport Byrds');
        $subject = 'Потвърдете регистрацията си — ' . $site;
        $body = "Здравейте, {$name},\n\n"
            . "Благодарим ви, че се регистрирахте в {$site}.\n"
            . "За да активирате акаунта си, отворете следния линк (валиден 48 часа):\n\n"
            . "{$url}\n\n"
            . "Ако не сте създавали акаунт, игнорирайте този имейл.\n\n"
            . "Екипът на {$site}";

        return MailService::send($user['email'], $subject, $body);
    }

    public static function verify(string $token): ?array
    {
        $token = trim($token);
        if ($token === '' || strlen($token) > 64) {
            return null;
        }
        $user = User::findByVerificationToken($token);
        if (!$user) {
            return null;
        }
        User::markEmailVerified((int) $user['id']);

        return User::find((int) $user['id']);
    }

    public static function resendForEmail(string $email): bool
    {
        $user = User::findByEmail($email);
        if (!$user || !empty($user['email_verified_at'])) {
            return true;
        }
        if (in_array($user['role'] ?? '', ['admin', 'super_admin'], true)) {
            return true;
        }
        $token = self::createToken();
        User::update((int) $user['id'], ['email_verification_token' => $token]);
        $user['email_verification_token'] = $token;

        return self::sendVerificationEmail($user);
    }

    public static function needsVerification(?array $user): bool
    {
        if (!$user) {
            return false;
        }
        if (in_array($user['role'] ?? '', ['admin', 'super_admin'], true)) {
            return false;
        }

        return empty($user['email_verified_at']);
    }
}
