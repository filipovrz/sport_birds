<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentConfig;
use App\Services\Payment\PaymentGatewayInterface;

/** ePay.bg — класически redirect с ENCODED + CHECKSUM (HMAC-SHA1). */
final class EpayGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'epay';
    }

    public function isConfigured(): bool
    {
        $cfg = PaymentConfig::gateway('epay');

        return trim((string) ($cfg['min'] ?? '')) !== ''
            && trim((string) ($cfg['secret'] ?? '')) !== '';
    }

    public function startCheckout(array $payment, string $returnUrl, string $cancelUrl): array
    {
        $cfg = PaymentConfig::gateway('epay');
        $min = (string) $cfg['min'];
        $secret = (string) $cfg['secret'];
        $url = rtrim((string) ($cfg['url'] ?: 'https://www.epay.bg/'), '/') . '/';
        $invoice = (string) $payment['id'];
        $amountBgn = number_format(
            (float) ($payment['amount_bgn'] ?? PaymentConfig::eurToBgn((float) $payment['amount_eur'])),
            2,
            '.',
            ''
        );
        $exp = date('d.m.Y H:i:s', strtotime('+3 days'));
        $descr = mb_substr($payment['description'] ?? 'Плащане', 0, 100);
        $data = "MIN={$min}\nINVOICE={$invoice}\nAMOUNT={$amountBgn}\nEXP_TIME={$exp}\nDESCR={$descr}";
        $encoded = base64_encode($data);
        $checksum = strtoupper(hash_hmac('sha1', $encoded, $secret));
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>ePay.bg</title></head><body>'
            . '<p>Пренасочване към ePay.bg…</p>'
            . '<form id="epay" method="post" action="' . htmlspecialchars($url) . '">'
            . '<input type="hidden" name="PAGE" value="paylogin">'
            . '<input type="hidden" name="ENCODED" value="' . htmlspecialchars($encoded) . '">'
            . '<input type="hidden" name="CHECKSUM" value="' . htmlspecialchars($checksum) . '">'
            . '<input type="hidden" name="URL_OK" value="' . htmlspecialchars($returnUrl) . '">'
            . '<input type="hidden" name="URL_CANCEL" value="' . htmlspecialchars($cancelUrl) . '">'
            . '</form><script>document.getElementById("epay").submit();</script></body></html>';

        return ['html' => $html];
    }

    public function verifyReturn(array $payment, array $query): ?string
    {
        $status = strtoupper(trim((string) ($query['STATUS'] ?? $query['status'] ?? '')));
        if (in_array($status, ['PAID', 'OK', '00'], true)) {
            return (string) ($query['INVOICE'] ?? $payment['id']);
        }

        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): ?array
    {
        parse_str($rawBody, $params);
        if ($params === []) {
            $params = $_POST;
        }
        $encoded = (string) ($params['ENCODED'] ?? '');
        $checksum = (string) ($params['CHECKSUM'] ?? '');
        if ($encoded === '' || $checksum === '') {
            return null;
        }
        $secret = (string) PaymentConfig::gateway('epay')['secret'];
        $expected = strtoupper(hash_hmac('sha1', $encoded, $secret));
        if (!hash_equals($expected, strtoupper($checksum))) {
            return null;
        }
        $decoded = base64_decode($encoded, true);
        if ($decoded === false) {
            return null;
        }
        $fields = [];
        foreach (explode("\n", $decoded) as $line) {
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $fields[$k] = $v;
            }
        }
        $status = strtoupper((string) ($fields['STATUS'] ?? $params['STATUS'] ?? ''));
        if (!in_array($status, ['PAID', 'OK', '00'], true) && ($fields['PAID'] ?? '') !== '1') {
            return null;
        }

        return [
            'gateway_payment_id' => $fields['INVOICE'] ?? '',
            'payment_id' => (int) ($fields['INVOICE'] ?? 0),
        ];
    }
}
