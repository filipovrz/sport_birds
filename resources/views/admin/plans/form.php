<h1><?= $plan ? 'Редакция на план' : 'Нов план' ?></h1>
<div class="card"><form method="post" action="<?= $plan ? '/admin/plans/'.$plan['id'] : '/admin/plans' ?>">
<div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($plan['name'] ?? '') ?>" required></div>
<?php if (!$plan): ?><div class="form-group"><label>Slug</label><input name="slug" required></div><?php endif; ?>
<div class="form-group"><label>Цена (лв)</label><input name="price_bgn" type="number" step="0.01" value="<?= $plan['price_bgn'] ?? 0 ?>"></div>
<div class="form-group"><label>Дни</label><input name="duration_days" type="number" value="<?= $plan['duration_days'] ?? 30 ?>"></div>
<label><input type="checkbox" name="is_active" <?= ($plan['is_active'] ?? 1) ? 'checked' : '' ?>> Активен</label>
<button class="btn btn-primary">Запази</button></form></div>
