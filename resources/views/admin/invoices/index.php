<h1><?= htmlspecialchars(__('admin.invoices_list')) ?></h1>
<div class="card table-scroll">
<table class="table">
    <thead>
        <tr>
            <th><?= htmlspecialchars(__('admin.invoice_type')) ?></th>
            <th><?= htmlspecialchars(__('admin.invoice_number')) ?></th>
            <th><?= htmlspecialchars(__('admin.invoice_date')) ?></th>
            <th><?= htmlspecialchars(__('admin.invoice_user')) ?></th>
            <th>Описание</th>
            <th><?= htmlspecialchars(__('admin.invoice_amount')) ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($invoices as $inv): ?>
        <tr>
            <td><?= htmlspecialchars(invoice_document_type_label($inv['document_type'] ?? 'invoice')) ?></td>
            <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
            <td><?= date('d.m.Y', strtotime($inv['issue_date'])) ?></td>
            <td><?= htmlspecialchars($inv['user_name'] ?? '') ?><br><small><?= htmlspecialchars($inv['user_email'] ?? '') ?></small></td>
            <td><?= htmlspecialchars($inv['line_description']) ?></td>
            <td><?= format_eur((float)$inv['amount_total_eur']) ?></td>
            <td>
                <a href="/admin/invoices/<?= (int)$inv['id'] ?>" class="btn btn-outline btn-sm"><?= htmlspecialchars(__('common.view')) ?></a>
                <a href="/admin/invoices/<?= (int)$inv['id'] ?>/print" class="btn btn-primary btn-sm" target="_blank" rel="noopener"><?= htmlspecialchars(__('common.print')) ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
