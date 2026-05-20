<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\User;

final class InvoiceService
{
    private const VAT_RATE = 20.0;

    public const TYPE_PROFORMA = 'proforma';
    public const TYPE_INVOICE = 'invoice';

    /** @return array<string, mixed>|null */
    public static function findByIdForUser(int $invoiceId, int $userId): ?array
    {
        return Database::fetch(
            'SELECT * FROM invoices WHERE id = ? AND user_id = ?',
            [$invoiceId, $userId]
        );
    }

    /** @return array<string, mixed>|null */
    public static function findForPayment(int $paymentId, string $documentType): ?array
    {
        return Database::fetch(
            'SELECT * FROM invoices WHERE payment_id = ? AND document_type = ?',
            [$paymentId, $documentType]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function listForUser(int $userId, int $limit = 100): array
    {
        return Database::fetchAll(
            'SELECT i.*, p.payable_type, p.payable_id, p.public_token, p.status AS payment_status
             FROM invoices i
             JOIN payments p ON p.id = i.payment_id
             WHERE i.user_id = ?
             ORDER BY i.issue_date DESC, i.id DESC
             LIMIT ' . max(1, min(500, $limit)),
            [$userId]
        );
    }

    /**
     * Проформа при банков превод (преди потвърждение).
     *
     * @return array<string, mixed>|null
     */
    public static function issueProformaForPayment(int $paymentId): ?array
    {
        $existing = self::findForPayment($paymentId, self::TYPE_PROFORMA);
        if ($existing) {
            return $existing;
        }

        $payment = PaymentService::findById($paymentId);
        if (!$payment || ($payment['method'] ?? '') !== 'bank') {
            return null;
        }
        if (in_array($payment['status'] ?? '', ['cancelled', 'failed', 'refunded'], true)) {
            return null;
        }

        return self::insertDocument($payment, self::TYPE_PROFORMA);
    }

    /**
     * Оригинална фактура след потвърдено плащане (идемпотентно).
     *
     * @return array<string, mixed>|null
     */
    public static function issueForPayment(int $paymentId): ?array
    {
        $existing = self::findForPayment($paymentId, self::TYPE_INVOICE);
        if ($existing) {
            return $existing;
        }

        $payment = PaymentService::findById($paymentId);
        if (!$payment || ($payment['status'] ?? '') !== 'paid') {
            return null;
        }

        $proforma = self::findForPayment($paymentId, self::TYPE_PROFORMA);

        return self::insertDocument($payment, self::TYPE_INVOICE, $proforma);
    }

    /**
     * @param array<string, mixed> $payment
     * @param array<string, mixed>|null $proforma
     * @return array<string, mixed>|null
     */
    private static function insertDocument(array $payment, string $documentType, ?array $proforma = null): ?array
    {
        $paymentId = (int) $payment['id'];
        $user = User::find((int) $payment['user_id']);
        if (!$user) {
            return null;
        }

        $seller = self::sellerSnapshot();
        $buyer = self::buyerSnapshot($user);
        $totalEur = (float) $payment['amount_eur'];
        $totalBgn = $payment['amount_bgn'] !== null ? (float) $payment['amount_bgn'] : null;
        $netEur = round($totalEur / (1 + self::VAT_RATE / 100), 2);
        $vatEur = round($totalEur - $netEur, 2);
        $reference = PaymentService::bankReference($payment);
        $methodSlug = (string) ($payment['method'] ?? '');
        $methodMeta = PaymentMethodsService::find($methodSlug);

        if ($documentType === self::TYPE_PROFORMA) {
            $issueDate = date('Y-m-d');
            $paidAt = null;
            $number = self::nextProformaNumber($issueDate);
        } else {
            $paidAt = (string) ($payment['paid_at'] ?? date('Y-m-d H:i:s'));
            $issueDate = date('Y-m-d', strtotime($paidAt));
            $number = self::nextInvoiceNumber($issueDate);
        }

        $id = Database::insert('invoices', [
            'payment_id' => $paymentId,
            'document_type' => $documentType,
            'user_id' => (int) $payment['user_id'],
            'invoice_number' => $number,
            'source_proforma_number' => $documentType === self::TYPE_INVOICE && $proforma
                ? (string) $proforma['invoice_number']
                : null,
            'issue_date' => $issueDate,
            'paid_at' => $paidAt,
            'seller_firm_name' => $seller['firm_name'] ?: null,
            'seller_eik' => $seller['eik'] ?: null,
            'seller_vat' => $seller['vat'] ?: null,
            'seller_address' => $seller['address'] ?: null,
            'seller_email' => $seller['email'] ?: null,
            'seller_phone' => $seller['phone'] ?: null,
            'buyer_name' => $buyer['name'],
            'buyer_email' => $buyer['email'] ?: null,
            'buyer_phone' => $buyer['phone'] ?: null,
            'buyer_address' => $buyer['address'] ?: null,
            'buyer_eik' => $buyer['eik'] ?: null,
            'buyer_vat' => $buyer['vat'] ?: null,
            'line_description' => mb_substr(trim((string) ($payment['description'] ?? 'Услуга')), 0, 500),
            'payment_method' => $methodMeta['label'] ?? $methodSlug,
            'payment_reference' => $reference,
            'amount_total_eur' => number_format($totalEur, 2, '.', ''),
            'amount_total_bgn' => $totalBgn !== null ? number_format($totalBgn, 2, '.', '') : null,
            'amount_net_eur' => number_format($netEur, 2, '.', ''),
            'amount_vat_eur' => number_format($vatEur, 2, '.', ''),
            'vat_rate' => number_format(self::VAT_RATE, 2, '.', ''),
            'currency' => (string) ($payment['currency'] ?? 'EUR'),
        ]);

        return Database::fetch('SELECT * FROM invoices WHERE id = ?', [$id]);
    }

    /** @return array{firm_name: string, eik: string, vat: string, address: string, email: string, phone: string} */
    public static function sellerSnapshot(): array
    {
        $company = FooterService::config()['company'] ?? [];

        return [
            'firm_name' => trim((string) ($company['firm_name'] ?? '')),
            'eik' => trim((string) ($company['eik'] ?? '')),
            'vat' => trim((string) ($company['vat'] ?? '')),
            'address' => trim((string) ($company['address'] ?? '')),
            'email' => trim((string) ($company['email'] ?? '')),
            'phone' => trim((string) ($company['phone'] ?? '')),
        ];
    }

    /** @param array<string, mixed> $user
     * @return array{name: string, email: string, phone: string, address: string, eik: string, vat: string}
     */
    private static function buyerSnapshot(array $user): array
    {
        $firm = trim((string) ($user['invoice_firm_name'] ?? ''));
        $name = $firm !== '' ? $firm : trim((string) ($user['name'] ?? ''));
        $addressParts = array_filter([
            trim((string) ($user['invoice_address'] ?? '')),
            trim((string) ($user['city'] ?? '')),
        ]);

        return [
            'name' => $name !== '' ? $name : 'Клиент',
            'email' => trim((string) ($user['email'] ?? '')),
            'phone' => trim((string) ($user['phone'] ?? '')),
            'address' => implode(', ', $addressParts),
            'eik' => trim((string) ($user['invoice_eik'] ?? '')),
            'vat' => trim((string) ($user['invoice_vat_id'] ?? '')),
        ];
    }

    private static function nextInvoiceNumber(string $issueDate): string
    {
        $year = date('Y', strtotime($issueDate));
        $prefix = trim((string) SettingsService::get('invoice_prefix', 'BSB'));
        if ($prefix === '') {
            $prefix = 'BSB';
        }
        $row = Database::fetch(
            'SELECT COUNT(*) AS c FROM invoices WHERE document_type = ? AND YEAR(issue_date) = ?',
            [self::TYPE_INVOICE, $year]
        );
        $seq = (int) ($row['c'] ?? 0) + 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $seq);
    }

    private static function nextProformaNumber(string $issueDate): string
    {
        $year = date('Y', strtotime($issueDate));
        $prefix = trim((string) SettingsService::get('proforma_prefix', 'PRO'));
        if ($prefix === '') {
            $prefix = 'PRO';
        }
        $row = Database::fetch(
            'SELECT COUNT(*) AS c FROM invoices WHERE document_type = ? AND YEAR(issue_date) = ?',
            [self::TYPE_PROFORMA, $year]
        );
        $seq = (int) ($row['c'] ?? 0) + 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $seq);
    }
}
