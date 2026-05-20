<h1>Фактури</h1>
<p class="text-muted">
    При <strong>банков превод</strong> първо се издава проформа с № на поръчката; след потвърдено плащане — оригинална фактура.
    При онлайн плащане се издава само фактура след успешно плащане.
    Данни за фактуриране: <a href="/dashboard/profile">профил</a>.
</p>

<?php if (empty($invoices)): ?>
<div class="card">
    <p>Нямате издадени документи. При банков превод проформата се появява веднага след избора на този метод.</p>
</div>
<?php else: ?>
<div class="card" style="overflow-x:auto">
    <table class="table">
        <thead>
            <tr>
                <th>Тип</th>
                <th>№</th>
                <th>Дата</th>
                <th>Поръчка / описание</th>
                <th>Референция</th>
                <th>Сума</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($invoices as $inv): ?>
            <tr>
                <td><?= htmlspecialchars(invoice_document_type_label($inv['document_type'] ?? 'invoice')) ?></td>
                <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
                <td><?= date('d.m.Y', strtotime($inv['issue_date'])) ?></td>
                <td><?= htmlspecialchars($inv['line_description']) ?></td>
                <td><code><?= htmlspecialchars($inv['payment_reference'] ?? '') ?></code></td>
                <td><?= format_eur((float) $inv['amount_total_eur']) ?></td>
                <td>
                    <a href="/dashboard/invoices/<?= (int) $inv['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
                    <a href="/dashboard/invoices/<?= (int) $inv['id'] ?>/print" class="btn btn-primary btn-sm" target="_blank" rel="noopener">Печат / PDF</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
