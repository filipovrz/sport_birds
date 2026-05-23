<h1><?= htmlspecialchars(invoice_document_type_label($invoice['document_type'] ?? 'invoice')) ?> <?= htmlspecialchars($invoice['invoice_number']) ?></h1>
<p class="text-muted"><?= htmlspecialchars($invoice['user_name'] ?? '') ?> · <?= htmlspecialchars($invoice['user_email'] ?? '') ?></p>
<p>
    <a href="/admin/invoices" class="btn btn-outline btn-sm">← <?= htmlspecialchars(__('admin.invoices_list')) ?></a>
    <a href="/admin/invoices/<?= (int)$invoice['id'] ?>/print" class="btn btn-primary btn-sm" target="_blank" rel="noopener"><?= htmlspecialchars(__('common.print')) ?></a>
</p>
<?php require BASE_PATH . '/resources/views/invoices/_body.php'; ?>
