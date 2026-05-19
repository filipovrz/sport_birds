<h1>Планове <a href="/admin/plans/create" class="btn btn-primary btn-sm">+ Нов</a></h1>
<div class="card"><table>
<tr><th>Име</th><th>Цена</th><th>Активен</th><th></th></tr>
<?php foreach ($plans as $p): ?>
<tr><td><?= htmlspecialchars($p['name']) ?></td><td><?= $p['price_bgn'] ?> лв</td>
<td><?= $p['is_active'] ? 'Да' : 'Не' ?></td>
<td><a href="/admin/plans/<?= (int)$p['id'] ?>/edit">Редакция</a></td></tr>
<?php endforeach; ?>
</table></div>
