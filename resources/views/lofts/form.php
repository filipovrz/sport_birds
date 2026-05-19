<h1><?= $loft ? 'Редакция' : 'Нов птицарник' ?></h1>
<div class="card"><form method="post" action="<?= $loft ? '/dashboard/lofts/'.$loft['id'] : '/dashboard/lofts' ?>
    <?= csrf_field() ?>">
<div class="form-group"><label>Име *</label><input name="name" required value="<?= htmlspecialchars($loft['name'] ?? '') ?>"></div>
<div class="form-group"><label>Локация</label><input name="location" value="<?= htmlspecialchars($loft['location'] ?? '') ?>"></div>
<div class="form-group"><label>Капацитет</label><input type="number" name="capacity" value="<?= htmlspecialchars((string)($loft['capacity'] ?? '')) ?>"></div>
<div class="form-group"><label>Бележки</label><textarea name="notes"><?= htmlspecialchars($loft['notes'] ?? '') ?></textarea></div>
<button class="btn btn-primary">Запази</button></form></div>
