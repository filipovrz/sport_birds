<?php

declare(strict_types=1);

namespace App\Services;

final class FooterService
{
    public static function config(): array
    {
        $raw = SettingsService::get('footer_json', '');
        if ($raw === null || trim($raw) === '') {
            return self::defaults();
        }
        $data = json_decode($raw, true);

        return is_array($data) ? array_replace_recursive(self::defaults(), $data) : self::defaults();
    }

    public static function defaults(): array
    {
        return [
            'enabled' => true,
            'tagline' => 'Управление на спортни птици — гълъби, GPS, състезания.',
            'address' => '',
            'phone' => '',
            'email' => '',
            'payment_title' => 'Начини на плащане',
            'payment_text' => "Банков превод\nРеференция при заявка за абонамент или обява",
            'copyright' => '',
            'columns' => [
                [
                    'title' => 'Информация',
                    'links' => [
                        ['label' => 'Цени', 'url' => '/pricing'],
                        ['label' => 'Общност', 'url' => '/community'],
                        ['label' => 'Обяви', 'url' => '/announcements'],
                    ],
                ],
                [
                    'title' => 'Правни документи',
                    'links' => [
                        ['label' => 'Поверителност', 'url' => '/legal/privacy'],
                        ['label' => 'Условия за ползване', 'url' => '/legal/terms'],
                        ['label' => 'Бисквитки', 'url' => '/legal/cookies'],
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, string> */
    public static function legalPages(): array
    {
        return [
            'privacy' => SettingsService::get('page_privacy_html', '') ?? '',
            'terms' => SettingsService::get('page_terms_html', '') ?? '',
            'cookies' => SettingsService::get('page_cookies_html', '') ?? '',
        ];
    }

    public static function legalTitle(string $slug): string
    {
        return match ($slug) {
            'privacy' => 'Политика за поверителност',
            'terms' => 'Общи условия',
            'cookies' => 'Бисквитки',
            default => 'Документ',
        };
    }

    public static function saveFromPost(array $post): void
    {
        $columns = [];
        for ($i = 1; $i <= 4; $i++) {
            $title = trim($post['column_title_' . $i] ?? '');
            $lines = trim($post['column_links_' . $i] ?? '');
            if ($title === '' && $lines === '') {
                continue;
            }
            $links = [];
            foreach (preg_split('/\r\n|\n|\r/', $lines) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parts = array_map('trim', explode('|', $line, 2));
                if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
                    $links[] = ['label' => $parts[0], 'url' => $parts[1]];
                }
            }
            $columns[] = ['title' => $title ?: 'Връзки', 'links' => $links];
        }

        $config = [
            'enabled' => isset($post['footer_enabled']),
            'tagline' => trim($post['footer_tagline'] ?? ''),
            'address' => trim($post['footer_address'] ?? ''),
            'phone' => trim($post['footer_phone'] ?? ''),
            'email' => trim($post['footer_email'] ?? ''),
            'payment_title' => trim($post['footer_payment_title'] ?? '') ?: 'Начини на плащане',
            'payment_text' => trim($post['footer_payment_text'] ?? ''),
            'copyright' => trim($post['footer_copyright'] ?? ''),
            'columns' => $columns,
        ];

        SettingsService::set('footer_json', json_encode($config, JSON_UNESCAPED_UNICODE));

        foreach (['privacy' => 'page_privacy_html', 'terms' => 'page_terms_html', 'cookies' => 'page_cookies_html'] as $slug => $key) {
            if (isset($post['legal_' . $slug])) {
                SettingsService::set($key, trim($post['legal_' . $slug]));
            }
        }
    }

    /** @return string */
    public static function columnsToText(array $column): string
    {
        $lines = [];
        foreach ($column['links'] ?? [] as $link) {
            if (!empty($link['label']) && !empty($link['url'])) {
                $lines[] = $link['label'] . ' | ' . $link['url'];
            }
        }

        return implode("\n", $lines);
    }
}
