<h1>Тренировки</h1>
<a href="/dashboard/training/create" class="btn btn-primary">+ Тренировка</a>
<div class="card"><table>
<tr><th>Дата</th><th>Гълъбарник</th><th>Дистанция</th><th>Върнали се</th></tr>
<?php foreach ($sessions as $s): ?>
<tr><td><?= htmlspecialchars($s['session_date']) ?></td><td><?= htmlspecialchars($s['loft_name'] ?? '—') ?></td>
<td><?= htmlspecialchars((string)($s['distance_km'] ?? '—')) ?> км</td>
<td><?= (int)($s['birds_returned'] ?? 0) ?>/<?= (int)($s['birds_released'] ?? 0) ?></td></tr>
<?php endforeach; ?>
</table></div>
