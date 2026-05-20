<?php

declare(strict_types=1);

namespace App\Services;

final class FooterService
{
    public static function config(): array
    {
        return self::normalize(self::rawConfig());
    }

    /** @return array<string, mixed> */
    public static function rawConfig(): array
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
        $year = date('Y');

        return [
            'enabled' => true,
            'tagline' => 'Управление на спортни птици — гълъби, GPS, състезания.',
            'address' => '',
            'phone' => '',
            'email' => '',
            'payment_title' => 'Начини на плащане',
            'payment_text' => '',
            'copyright' => 'Evtinko © ' . $year . ' Best Sport Byrds',
            'company' => [
                'title' => 'Информация',
                'firm_name' => '',
                'eik' => '',
                'vat' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
                'other' => '',
            ],
            'columns' => [
                [
                    'title' => 'Правни документи',
                    'links' => [
                        ['label' => 'Поверителност', 'url' => '/legal/privacy'],
                        ['label' => 'GDPR', 'url' => '/legal/gdpr'],
                        ['label' => 'Условия за ползване', 'url' => '/legal/terms'],
                        ['label' => 'Бисквитки', 'url' => '/legal/cookies'],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $cfg */
    private static function normalize(array $cfg): array
    {
        $defaults = self::defaults();
        $cfg['company'] = array_merge($defaults['company'], is_array($cfg['company'] ?? null) ? $cfg['company'] : []);

        $cfg['columns'] = array_values(array_filter(
            $cfg['columns'] ?? [],
            static function (array $col): bool {
                $title = (string) ($col['title'] ?? '');
                if (preg_match('/начини на плащане|плащан/i', $title)) {
                    return false;
                }
                if ($title !== 'Информация') {
                    return true;
                }
                $labels = array_column($col['links'] ?? [], 'label');

                return !array_intersect($labels, ['Цени', 'Общност', 'Обяви']);
            }
        ));

        $copyright = trim((string) ($cfg['copyright'] ?? ''));
        if ($copyright === '' || preg_match('/^©\s*\d{4}/u', $copyright)) {
            $cfg['copyright'] = 'Evtinko © ' . date('Y') . ' Best Sport Byrds';
        } elseif (stripos($copyright, 'Evtinko') === false && stripos($copyright, 'Best Sport Byrds') !== false) {
            $cfg['copyright'] = 'Evtinko ' . $copyright;
        }

        $oldPayment = "Банков превод\nРеференция при заявка за абонамент или обява";
        $paymentText = trim((string) ($cfg['payment_text'] ?? ''));
        if ($paymentText === $oldPayment || PaymentMethodsService::looksLikeAccidentalPaste($paymentText)) {
            $cfg['payment_text'] = '';
        }

        $cfg['columns'] = self::ensureLegalLinks($cfg['columns'] ?? []);

        return $cfg;
    }

    /** @param list<array<string, mixed>> $columns */
    private static function ensureLegalLinks(array $columns): array
    {
        $required = [
            ['label' => 'Поверителност', 'url' => '/legal/privacy'],
            ['label' => 'GDPR', 'url' => '/legal/gdpr'],
            ['label' => 'Условия за ползване', 'url' => '/legal/terms'],
            ['label' => 'Бисквитки', 'url' => '/legal/cookies'],
        ];
        foreach ($columns as &$col) {
            if (($col['title'] ?? '') !== 'Правни документи') {
                continue;
            }
            $urls = array_column($col['links'] ?? [], 'url');
            foreach ($required as $link) {
                if (!in_array($link['url'], $urls, true)) {
                    $col['links'][] = $link;
                }
            }
        }
        unset($col);

        return $columns;
    }

    /** @return list<array{label: string, value: string, href: ?string}> */
    public static function companyLines(array $company): array
    {
        $lines = [];
        $map = [
            'firm_name' => 'Фирма',
            'eik' => 'ЕИК',
            'vat' => 'ДДС №',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'email' => 'Имейл',
            'website' => 'Уебсайт',
            'other' => 'Друго',
        ];
        foreach ($map as $key => $label) {
            $value = trim((string) ($company[$key] ?? ''));
            if ($value === '') {
                continue;
            }
            $href = null;
            if ($key === 'email') {
                $href = 'mailto:' . $value;
            } elseif ($key === 'phone') {
                $href = 'tel:' . preg_replace('/\s+/', '', $value);
            } elseif ($key === 'website' && !str_starts_with($value, 'http')) {
                $href = 'https://' . $value;
            } elseif ($key === 'website') {
                $href = $value;
            }
            $lines[] = ['label' => $label, 'value' => $value, 'href' => $href];
        }

        return $lines;
    }

    public static function hasCompanyInfo(array $company): bool
    {
        return self::companyLines($company) !== [];
    }

    /** @return array<string, string> */
    public static function legalPages(): array
    {
        return \App\Services\LegalContentService::allPages();
    }

    public static function legalTitle(string $slug): string
    {
        return \App\Services\LegalContentService::title($slug);
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
            'payment_text' => '',
            'copyright' => trim($post['footer_copyright'] ?? ''),
            'company' => [
                'title' => trim($post['company_title'] ?? '') ?: 'Информация',
                'firm_name' => trim($post['company_firm_name'] ?? ''),
                'eik' => trim($post['company_eik'] ?? ''),
                'vat' => trim($post['company_vat'] ?? ''),
                'address' => trim($post['company_address'] ?? ''),
                'phone' => trim($post['company_phone'] ?? ''),
                'email' => trim($post['company_email'] ?? ''),
                'website' => trim($post['company_website'] ?? ''),
                'other' => trim($post['company_other'] ?? ''),
            ],
            'columns' => $columns,
        ];

        SettingsService::set('footer_json', json_encode($config, JSON_UNESCAPED_UNICODE));

        foreach (['privacy' => 'page_privacy_html', 'terms' => 'page_terms_html', 'cookies' => 'page_cookies_html', 'gdpr' => 'page_gdpr_html'] as $slug => $key) {
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
