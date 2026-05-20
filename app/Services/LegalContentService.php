<?php

declare(strict_types=1);

namespace App\Services;

final class LegalContentService
{
    private const SLUGS = ['privacy', 'terms', 'cookies', 'gdpr'];

    public static function slugExists(string $slug): bool
    {
        return in_array($slug, self::SLUGS, true);
    }

    public static function title(string $slug): string
    {
        return match ($slug) {
            'privacy' => 'Политика за поверителност',
            'terms' => 'Общи условия за ползване',
            'cookies' => 'Политика за бисквитки',
            'gdpr' => 'GDPR — информация за субектите на данни',
            default => 'Правен документ',
        };
    }

    public static function content(string $slug): string
    {
        if (!self::slugExists($slug)) {
            return '';
        }
        $key = 'page_' . $slug . '_html';
        $stored = trim(SettingsService::get($key, '') ?? '');
        $raw = $stored !== '' ? $stored : self::loadTemplate($slug);

        return self::applyPlaceholders($raw);
    }

    public static function formatHtml(string $content): string
    {
        $html = '';
        foreach (preg_split('/\r\n|\n|\r/', $content) as $line) {
            $trim = trim($line);
            if ($trim === '') {
                $html .= '<br>';
                continue;
            }
            if (preg_match('/^(\d+\.)\s+.+$/u', $trim) && mb_strlen($trim) < 140
                && !preg_match('/[;,:]\s*$/u', $trim)) {
                $html .= '<h2 class="legal-h2">' . htmlspecialchars($trim) . '</h2>';
            } elseif (preg_match('/^[А-ЯA-Z][А-ЯA-Z0-9\s\-\–\(\)]{5,}$/u', $trim) && mb_strlen($trim) < 90) {
                $html .= '<h2 class="legal-h2">' . htmlspecialchars($trim) . '</h2>';
            } else {
                $html .= '<p>' . htmlspecialchars($trim) . '</p>';
            }
        }

        return $html;
    }

    /** @return array<string, string> */
    public static function allPages(): array
    {
        $pages = [];
        foreach (self::SLUGS as $slug) {
            $pages[$slug] = self::content($slug);
        }

        return $pages;
    }

    private static function loadTemplate(string $slug): string
    {
        $path = BASE_PATH . '/resources/legal/' . $slug . '.bg.txt';
        if (!is_file($path)) {
            return '';
        }

        return (string) file_get_contents($path);
    }

    private static function applyPlaceholders(string $text): string
    {
        $config = require BASE_PATH . '/config/app.php';
        $footer = FooterService::config();
        $company = $footer['company'] ?? [];
        $site = SettingsService::get('site_name') ?: ($config['name'] ?? 'Best Sport Byrds');
        $email = SettingsService::get('contact_email')
            ?: ($company['email'] ?? '')
            ?: ($footer['email'] ?? 'privacy@bestsportbyrds.local');
        $firm = trim($company['firm_name'] ?? '') ?: 'Evtinko (администратор на платформата Best Sport Byrds)';
        $eik = trim($company['eik'] ?? '') ?: '[ЕИК — попълнете в админ → Футър]';
        $address = trim($company['address'] ?? '') ?: trim($footer['address'] ?? '') ?: '[адрес — попълнете в админ → Футър]';
        $phone = trim($company['phone'] ?? '') ?: trim($footer['phone'] ?? '') ?: '[телефон]';
        $url = rtrim($_ENV['APP_URL'] ?? $config['url'] ?? 'http://localhost:8080', '/');
        $date = date('d.m.Y');

        $map = [
            '{SITE_NAME}' => $site,
            '{APP_URL}' => $url,
            '{CONTACT_EMAIL}' => $email,
            '{COMPANY_NAME}' => $firm,
            '{COMPANY_EIK}' => $eik,
            '{COMPANY_ADDRESS}' => $address,
            '{COMPANY_PHONE}' => $phone,
            '{DATE}' => $date,
            '{YEAR}' => date('Y'),
        ];

        return str_replace(array_keys($map), array_values($map), $text);
    }
}
