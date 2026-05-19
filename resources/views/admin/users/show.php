<h1><?= htmlspecialchars($u['name']) ?></h1>
<div class="card"><form method="post" action="/admin/users/<?= (int)$u['id'] ?>
    <?= csrf_field() ?>">
<label><input type="checkbox" name="is_active" <?= $u['is_active'] ? 'checked' : '' ?>> Активен</label>
<div class="form-group"><label>Роля</label>
<select name="role">
<option value="user" <?= $u['role']==='user'?'selected':'' ?>>Потребител</option>
<option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Админ</option>
</select></div>
<div class="form-group"><label>Изтича на</label><input type="datetime-local" name="subscription_expires_at" value="<?= $u['subscription_expires_at'] ? date('Y-m-d\TH:i', strtotime($u['subscription_expires_at'])) : '' ?>"></div>
<button class="btn btn-primary">Запази</button></form></div>
