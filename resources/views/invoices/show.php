<h1><?= htmlspecialchars(invoice_document_type_label($invoice['document_type'] ?? 'invoice')) ?> <?= htmlspecialchars($invoice['invoice_number']) ?></h1>
<p>
    <a href="/dashboard/invoices" class="btn btn-outline btn-sm">← Всички фактури</a>
    <a href="/dashboard/invoices/<?= (int) $invoice['id'] ?>/print" class="btn btn-primary btn-sm" target="_blank" rel="noopener">Печат / Запази като PDF</a>
</p>
<?php require __DIR__ . '/_body.php'; ?>
