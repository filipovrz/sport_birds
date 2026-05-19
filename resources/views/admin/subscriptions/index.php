<h1>Заявки за абонамент</h1>
<div class="card"><table>
<tr><th>Потребител</th><th>План</th><th>Статус</th><th>Реф.</th><th></th></tr>
<?php foreach ($requests as $r): ?>
<tr>
<td><?= htmlspecialchars($r['user_name']) ?> (<?= htmlspecialchars($r['email']) ?>)</td>
<td><?= htmlspecialchars($r['plan_name']) ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= htmlspecialchars($r['payment_reference'] ?? '—') ?></td>
<td>
<?php if ($r['status'] === 'pending'): ?>
<form method="post" action="/admin/subscriptions/<?= (int)$r['id'] ?>
    <?= csrf_field() ?>/approve" style="display:inline"><button class="btn btn-primary btn-sm">Одобри</button></form>
<form method="post" action="/admin/subscriptions/<?= (int)$r['id'] ?>
    <?= csrf_field() ?>/reject" style="display:inline"><button class="btn btn-danger btn-sm">Отхвърли</button></form>
<?php endif; ?>
</td></tr>
<?php endforeach; ?>
</table></div>
