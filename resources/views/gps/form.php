<h1><?= $device ? 'Редакция на GPS' : 'Регистрация на GPS устройство' ?></h1>
<div class="card">
<form method="post" action="<?= $device ? '/dashboard/gps/'.$device['id'] : '/dashboard/gps' ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име *</label><input name="name" required value="<?= htmlspecialchars($device['name'] ?? '') ?>"></div>
    <div class="form-group"><label>Сериен номер *</label><input name="serial_number" required value="<?= htmlspecialchars($device['serial_number'] ?? '') ?>" <?= $device ? 'readonly' : '' ?>></div>
    <div class="form-group"><label>Модел</label><input name="model" value="<?= htmlspecialchars($device['model'] ?? '') ?>"></div>
    <div class="form-group"><label>Свързана птица</label>
        <select name="bird_id"><option value="">—</option>
        <?php foreach ($birds as $b): ?>
        <option value="<?= (int)$b['id'] ?>" <?= (int)($device['bird_id'] ?? 0) === (int)$b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['ring_number']) ?></option>
        <?php endforeach; ?>
        </select>
    </div>
    <?php if ($device): ?>
    <label><input type="checkbox" name="is_active" <?= $device['is_active'] ? 'checked' : '' ?>> Активно</label>
    <?php endif; ?>
    <div class="form-group"><label>Бележки</label><textarea name="notes"><?= htmlspecialchars($device['notes'] ?? '') ?></textarea></div>
    <button class="btn btn-primary">Запази</button>
</form>
</div>
