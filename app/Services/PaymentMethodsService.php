<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Единен източник за начини на плащане (футър, абонаменти, обяви).
 */
final class PaymentMethodsService
{
    /** @return list<array{name: string, automatic: bool, note: string}> */
    public static function defaults(): array
    {
        return [
            [
                'name' => 'Банков превод',
                'automatic' => false,
                'note' => 'Одобрение от администратор след постъпване на сумата',
            ],
            [
                'name' => 'Кредитна/дебитна карта',
                'automatic' => true,
                'note' => 'Автоматично отчитане',
            ],
            [
                'name' => 'ePay.bg',
                'automatic' => true,
                'note' => 'Автоматично отчитане',
            ],
            [
                'name' => 'Други интегрирани методи',
                'automatic' => true,
                'note' => 'При налична интеграция',
            ],
        ];
    }

    public static function footerNote(): string
    {
        return 'Всички плащания освен банковия превод се обработват автоматично '
            . 'и потребителят получава заявената услуга или достъп.';
    }

    /** @return list<array{name: string, automatic: bool, note: string}> */
    public static function forFooter(): array
    {
        $raw = SettingsService::get('payment_methods_json', '');
        if ($raw !== null && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && $decoded !== []) {
                return self::sanitizeList($decoded);
            }
        }

        $footer = FooterService::rawConfig();
        if (!empty($footer['payment_methods']) && is_array($footer['payment_methods'])) {
            return self::sanitizeList($footer['payment_methods']);
        }

        $text = trim((string) ($footer['payment_text'] ?? ''));
        if ($text !== '' && !self::looksLikeAccidentalPaste($text)) {
            return self::fromTextLines($text);
        }

        return self::defaults();
    }

    /** Текст за форми (абонамент, обяви) — банкови реквизити + методи. */
    public static function instructionsForForms(): string
    {
        $custom = trim(SettingsService::get('payment_instructions', '') ?? '');
        $bankBlock = '';
        if ($custom !== '' && !self::looksLikeAccidentalPaste($custom)) {
            $bankBlock = $custom . "\n\n";
        }

        $lines = ["Начини на плащане:"];
        foreach (self::forFooter() as $m) {
            $tag = $m['automatic'] ? 'автоматично' : 'ръчно одобрение';
            $lines[] = '• ' . $m['name'] . ' — ' . $tag;
        }
        $lines[] = '';
        $lines[] = self::footerNote();

        return $bankBlock . implode("\n", $lines);
    }

    /** @param list<array<string, mixed>> $list */
    public static function sanitizeList(array $list): array
    {
        $out = [];
        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }
            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $out[] = [
                'name' => $name,
                'automatic' => !empty($item['automatic']),
                'note' => trim((string) ($item['note'] ?? '')),
            ];
        }

        return $out !== [] ? $out : self::defaults();
    }

    /** @param list<string> $lines */
    public static function fromTextLines(string $lines): array
    {
        $methods = [];
        foreach (preg_split('/\r\n|\n|\r/', $lines) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $automatic = true;
            if (preg_match('/банков|превод/i', $line)) {
                $automatic = false;
            }
            $methods[] = [
                'name' => preg_replace('/\s*[\-—].*$/u', '', $line),
                'automatic' => $automatic,
                'note' => $automatic ? 'Автоматично отчитане' : 'Ръчна обработка',
            ];
        }

        return self::sanitizeList($methods);
    }

    public static function methodsToText(array $methods): string
    {
        $lines = [];
        foreach ($methods as $m) {
            $lines[] = $m['name'];
        }

        return implode("\n", $lines);
    }

    public static function looksLikeAccidentalPaste(string $text): bool
    {
        return (bool) preg_match('/стават\s*:/ui', $text)
            || str_contains($text, 'Начини на плащане стават')
            || str_contains($text, 'копирал');
    }

    public static function saveFromPost(array $post): void
    {
        $methods = self::fromTextLines(trim($post['payment_methods_lines'] ?? ''));
        SettingsService::set('payment_methods_json', json_encode($methods, JSON_UNESCAPED_UNICODE));
        SettingsService::set('payment_footer_note', trim($post['payment_footer_note'] ?? ''));
        $bank = trim($post['payment_instructions'] ?? '');
        if ($bank !== '' && !self::looksLikeAccidentalPaste($bank)) {
            SettingsService::set('payment_instructions', $bank);
        }
    }

    public static function footerNoteStored(): string
    {
        $n = trim(SettingsService::get('payment_footer_note', '') ?? '');

        return $n !== '' ? $n : self::footerNote();
    }
}
