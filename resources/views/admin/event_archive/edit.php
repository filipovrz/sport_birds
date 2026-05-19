<p><a href="/admin/event-archive/<?= (int)$ev['id'] ?>">← Назад</a></p>
<h1>Редакция: <?= htmlspecialchars($ev['title']) ?></h1>
<div class="card">
<form method="post" action="/admin/event-archive/<?= (int)$ev['id'] ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Заглавие *</label><input name="title" value="<?= htmlspecialchars($ev['title']) ?>" required></div>
    <div class="form-group"><label>Вид</label><select name="event_type">
        <?php foreach (['gathering','assembly','meeting','exhibition','social','other'] as $t): ?>
        <option value="<?= $t ?>" <?= ($ev['event_type'] ?? '') === $t ? 'selected' : '' ?>><?= event_type_label($t) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="form-group"><label>Описание</label><textarea name="description" rows="3"><?= htmlspecialchars($ev['description'] ?? '') ?></textarea></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Дата *</label><input type="date" name="event_date" value="<?= htmlspecialchars($ev['event_date']) ?>" required></div>
        <div class="form-group"><label>Крайна дата</label><input type="date" name="event_end_date" value="<?= htmlspecialchars($ev['event_end_date'] ?? '') ?>"></div>
        <div class="form-group"><label>Място</label><input name="location" value="<?= htmlspecialchars($ev['location'] ?? '') ?>"></div>
        <div class="form-group"><label>Макс. участници</label><input name="max_participants" type="number" value="<?= $ev['max_participants'] !== null ? (int)$ev['max_participants'] : '' ?>"></div>
    </div>
    <div class="grid grid-2">
        <div class="form-group"><label>Статус</label><select name="status">
            <?php foreach (['draft','published','cancelled','completed'] as $st): ?>
            <option value="<?= $st ?>" <?= ($ev['status'] ?? '') === $st ? 'selected' : '' ?>><?= announcement_status_label($st) ?></option>
            <?php endforeach; ?>
        </select></div>
        <div class="form-group"><label>Плащане</label><select name="payment_status">
            <?php foreach (['not_required','pending','approved','rejected'] as $ps): ?>
            <option value="<?= $ps ?>" <?= ($ev['payment_status'] ?? '') === $ps ? 'selected' : '' ?>><?= announcement_payment_status_label($ps) ?></option>
            <?php endforeach; ?>
        </select></div>
    </div>
    <label><input type="checkbox" name="is_featured" <?= !empty($ev['is_featured']) ? 'checked' : '' ?>> Препоръчано</label>
    <p style="margin-top:1rem"><button class="btn btn-primary">Запази</button></p>
</form>
</div>
