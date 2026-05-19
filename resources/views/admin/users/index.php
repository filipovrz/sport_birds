<h1>Потребители</h1>
<div class="card"><table>
<tr><th>Име</th><th>Имейл</th><th>Роля</th><th>План</th><th></th></tr>
<?php foreach ($users as $u): ?>
<tr><td><?= htmlspecialchars($u['name']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
<td><?= role_label($u['role']) ?></td><td><?= htmlspecialchars($u['plan_name'] ?? '—') ?></td>
<td><a href="/admin/users/<?= (int)$u['id'] ?>">Управление</a></td></tr>
<?php endforeach; ?>
</table></div>
