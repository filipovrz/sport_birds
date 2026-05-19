<h1>Здраве и лечение</h1>
<a href="/dashboard/health/create" class="btn btn-primary">+ Запис</a>
<div class="card"><table>
<tr><th>Дата</th><th>Заглавие</th><th>Птица</th><th>Следващ</th><th></th></tr>
<?php foreach ($records as $r): ?>
<tr><td><?= htmlspecialchars($r['recorded_at']) ?></td><td><?= htmlspecialchars($r['title']) ?></td>
<td><?= htmlspecialchars($r['ring_number'] ?? 'Общо') ?></td>
<td><?= htmlspecialchars($r['next_due_at'] ?? '—') ?></td>
<td><a href="/dashboard/health/<?= (int)$r['id'] ?>/edit">Редакция</a></td></tr>
<?php endforeach; ?>
</table></div>
