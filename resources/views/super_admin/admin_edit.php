<p><a href="/super-admin/admins">← Към администраторите</a></p>
<h1>Редакция: <?= htmlspecialchars($admin['name']) ?></h1>
<div class="card">
<form method="post" action="/super-admin/admins/<?= (int)$admin['id'] ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($admin['name']) ?>" required></div>
    <div class="form-group"><label>Имейл</label><input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></div>
    <div class="form-group"><label>Нова парола</label><input type="password" name="password" placeholder="Празно = без промяна"></div>
    <h2 style="font-size:1.1rem;margin:1.25rem 0 0.5rem">Права за администриране</h2>
    <p style="color:var(--muted);font-size:0.9rem">Отбележете секциите, до които администраторът има достъп.</p>
    <?php foreach ($permissionLabels as $key => $label): ?>
    <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.45rem">
        <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($key) ?>" <?= in_array($key, $granted, true) ? 'checked' : '' ?>>
        <?= htmlspecialchars($label) ?>
    </label>
    <?php endforeach; ?>
    <p style="margin-top:1rem"><button type="submit" class="btn btn-primary">Запази</button></p>
</form>
</div>
