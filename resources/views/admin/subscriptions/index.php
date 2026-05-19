<h1>Заявки за абонамент</h1>
<p style="color:var(--muted);margin-top:0">
    <span class="status-with-dot"><span class="status-dot status-dot--approved" aria-hidden="true"></span> Одобрена</span>
    ·
    <span class="status-with-dot"><span class="status-dot status-dot--rejected" aria-hidden="true"></span> Отхвърлена</span>
    ·
    <span class="status-with-dot"><span class="status-dot status-dot--pending" aria-hidden="true"></span> Чака одобрение</span>
</p>
<div class="card table-scroll"><table>
<tr><th>Статус</th><th>Потребител</th><th>План и сума</th><th>Дата</th><th>Референция</th><th></th></tr>
<?php foreach ($requests as $r): ?>
<tr>
<td><?= subscription_request_status_html($r['status']) ?></td>
<td><?= htmlspecialchars($r['user_name']) ?></td>
<td><?= htmlspecialchars($r['plan_name']) ?> — <?= format_eur((float)($r['price_eur'] ?? 0)) ?></td>
<td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
<td><?= htmlspecialchars($r['payment_reference'] ?? '—') ?></td>
<td style="white-space:nowrap">
<a href="/admin/subscriptions/<?= (int)$r['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
<?php if ($r['status'] === 'pending'): ?>
<form method="post" action="/admin/subscriptions/<?= (int)$r['id'] ?>/approve" style="display:inline">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-primary btn-sm">Одобри</button>
</form>
<form method="post" action="/admin/subscriptions/<?= (int)$r['id'] ?>/reject" style="display:inline;margin-left:0.25rem">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger btn-sm">Отхвърли</button>
</form>
<?php endif; ?>
<?php
$deleteAction = '/admin/subscriptions/' . (int)$r['id'] . '/delete';
$deleteConfirm = 'Сигурни ли сте, че искате да изтриете заявката #' . (int)$r['id'] . '?';
require BASE_PATH . '/resources/views/admin/_delete_form.php';
?>
</td>
</tr>
<?php endforeach; ?>
</table></div>
