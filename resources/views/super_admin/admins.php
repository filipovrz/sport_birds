<h1>Администратори</h1>
<p style="color:var(--muted)">Супер администраторът създава админи и задава права по секции.</p>
<div class="card table-scroll"><table>
<tr><th>Име</th><th>Имейл</th><th>Роля</th><th>Права</th><th></th></tr>
<?php foreach ($admins as $a): ?>
<tr>
<td><?= htmlspecialchars($a['name']) ?></td>
<td><?= htmlspecialchars($a['email']) ?></td>
<td><?= role_label($a['role']) ?></td>
<td><?php if (($a['role'] ?? '') === 'super_admin'): ?>
    <span style="color:var(--muted)">Всички</span>
<?php else:
    $perms = \App\Services\AdminPermissionService::permissionsForUser($a);
    if ($perms === null) {
        echo '<span style="color:var(--muted)">Всички (наследено)</span>';
    } elseif ($perms === []) {
        echo '—';
    } else {
        $labels = [];
        foreach ($perms as $p) {
            $labels[] = $permissionLabels[$p] ?? $p;
        }
        echo htmlspecialchars(implode(', ', $labels));
    }
endif; ?></td>
<td style="white-space:nowrap">
<?php if (($a['role'] ?? '') === 'admin'): ?>
<a href="/super-admin/admins/<?= (int)$a['id'] ?>/edit" class="btn btn-outline btn-sm">Права</a>
<form method="post" action="/super-admin/admins/<?= (int)$a['id'] ?>/revoke" style="display:inline">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger btn-sm">Премахни админ</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table></div>
<div class="card" style="margin-top:1.25rem">
<h3 style="margin-top:0">Нов администратор</h3>
<form method="post" action="/super-admin/admins">
    <?= csrf_field() ?>
    <div class="grid grid-2">
        <div class="form-group"><label>Име</label><input name="name" required></div>
        <div class="form-group"><label>Имейл</label><input name="email" type="email" required></div>
    </div>
    <div class="form-group"><label>Парола</label><input name="password" type="password" required></div>
    <h3 style="font-size:1rem">Права</h3>
    <?php foreach ($permissionLabels as $key => $label): ?>
    <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.35rem">
        <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($key) ?>" checked>
        <?= htmlspecialchars($label) ?>
    </label>
    <?php endforeach; ?>
    <p style="margin-top:1rem"><button type="submit" class="btn btn-primary">Създай администратор</button></p>
</form>
</div>
