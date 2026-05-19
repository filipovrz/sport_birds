<h1><?= $record ? 'Редакция' : 'Нов здравен запис' ?></h1>
<div class="card"><form method="post" action="<?= $record ? '/dashboard/health/'.$record['id'] : '/dashboard/health' ?>">
    <?= csrf_field() ?>
<div class="form-group"><label>Заглавие *</label><input name="title" required value="<?= htmlspecialchars($record['title'] ?? '') ?>"></div>
<div class="form-group"><label>Тип</label><select name="record_type">
<?php foreach (['vaccination','treatment','illness','parasite','checkup','other'] as $t): ?>
<option value="<?= $t ?>" <?= ($record['record_type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
<?php endforeach; ?></select></div>
<div class="form-group"><label>Птица</label><select name="bird_id"><option value="">—</option>
<?php foreach ($birds as $b): ?>
<option value="<?= (int)$b['id'] ?>" <?= (int)($record['bird_id'] ?? 0) === (int)$b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['ring_number']) ?></option>
<?php endforeach; ?>
</select></div>
<div class="form-group"><label>Дата *</label><input type="date" name="recorded_at" required value="<?= htmlspecialchars($record['recorded_at'] ?? date('Y-m-d')) ?>"></div>
<div class="form-group"><label>Следващ преглед</label><input type="date" name="next_due_at" value="<?= htmlspecialchars($record['next_due_at'] ?? '') ?>"></div>
<div class="form-group"><label>Лечение</label><textarea name="treatment"><?= htmlspecialchars($record['treatment'] ?? '') ?></textarea></div>
<button class="btn btn-primary">Запази</button></form></div>
