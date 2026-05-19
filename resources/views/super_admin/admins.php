<h1>Администратори</h1>
<div class="card"><table>
<tr><th>Име</th><th>Имейл</th><th>Роля</th><th></th></tr>
<?php foreach ($admins as $a): ?>
<tr><td><?= htmlspecialchars($a['name']) ?></td><td><?= htmlspecialchars($a['email']) ?></td>
<td><?= role_label($a['role']) ?></td>
<td><?php if ($a['role']==='admin'): ?>
<form method="post" action="/super-admin/admins/<?= (int)$a['id'] ?>
    <?= csrf_field() ?>/revoke"><button class="btn btn-danger btn-sm">Премахни админ</button></form>
<?php endif; ?></td></tr>
<?php endforeach; ?>
</table>
<h3>Нов администратор</h3>
<form method="post" action="/super-admin/admins">
    <?= csrf_field() ?>
<input name="name" placeholder="Име" required>
<input name="email" type="email" placeholder="Имейл" required>
<input name="password" type="password" placeholder="Парола" required>
<button class="btn btn-primary">Създай</button>
</form>
</div>
