<p><a href="/admin/competition-archive/<?= (int)$ann['id'] ?>">← Към прегледа</a></p>
<h1>Редакция: <?= htmlspecialchars($ann['title']) ?></h1>
<div class="card">
<form method="post" action="/admin/competition-archive/<?= (int)$ann['id'] ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Заглавие *</label><input name="title" value="<?= htmlspecialchars($ann['title']) ?>" required></div>
    <div class="form-group"><label>Описание</label><textarea name="description" rows="4"><?= htmlspecialchars($ann['description'] ?? '') ?></textarea></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Тип</label>
            <select name="competition_type">
                <?php
                $typeOptions = competition_type_options();
                if (!empty($ann['competition_type']) && !in_array($ann['competition_type'], $typeOptions, true)) {
                    $typeOptions[] = $ann['competition_type'];
                }
                foreach ($typeOptions as $t):
                ?>
                <option value="<?= $t ?>" <?= ($ann['competition_type'] ?? '') === $t ? 'selected' : '' ?>><?= competition_type_label($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Вид</label>
            <select name="species">
                <?php foreach (['racing_pigeon','sport_pigeon','gamecock','other'] as $s): ?>
                <option value="<?= $s ?>" <?= ($ann['species'] ?? '') === $s ? 'selected' : '' ?>><?= competition_species_label($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Дата на събитие *</label><input type="date" name="event_date" value="<?= htmlspecialchars($ann['event_date']) ?>" required></div>
        <div class="form-group"><label>Краен срок за запис</label><input type="date" name="registration_deadline" value="<?= htmlspecialchars($ann['registration_deadline'] ?? '') ?>"></div>
        <div class="form-group"><label>Място</label><input name="location" value="<?= htmlspecialchars($ann['location'] ?? '') ?>"></div>
        <div class="form-group"><label>Дистанция (км)</label><input name="distance_km" type="number" step="0.1" value="<?= htmlspecialchars((string)($ann['distance_km'] ?? '')) ?>"></div>
    </div>
    <input type="hidden" name="latitude" id="lat" value="<?= htmlspecialchars((string)($ann['latitude'] ?? '')) ?>">
    <input type="hidden" name="longitude" id="lng" value="<?= htmlspecialchars((string)($ann['longitude'] ?? '')) ?>">
    <p>Локация на картата:</p>
    <div id="ann-map" style="height:260px;border-radius:8px;margin-bottom:1rem"></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Клуб</label><input name="club_name" value="<?= htmlspecialchars($ann['club_name'] ?? '') ?>"></div>
        <div class="form-group"><label>Организатор</label><input name="organizer" value="<?= htmlspecialchars($ann['organizer'] ?? '') ?>"></div>
        <div class="form-group"><label>Макс. участници</label><input name="max_participants" type="number" min="0" value="<?= $ann['max_participants'] !== null ? (int)$ann['max_participants'] : '' ?>"></div>
        <div class="form-group"><label>Такса за участие (€)</label><input name="entry_fee_bgn" type="number" step="0.01" value="<?= htmlspecialchars((string)($ann['entry_fee_bgn'] ?? '')) ?>"></div>
    </div>
    <div class="grid grid-2">
        <div class="form-group"><label>Контакт имейл</label><input name="contact_email" type="email" value="<?= htmlspecialchars($ann['contact_email'] ?? '') ?>"></div>
        <div class="form-group"><label>Контакт телефон</label><input name="contact_phone" value="<?= htmlspecialchars($ann['contact_phone'] ?? '') ?>"></div>
    </div>
    <div class="grid grid-2">
        <div class="form-group"><label>Статус на обявата</label>
            <select name="status">
                <?php foreach (['draft','published','cancelled','completed'] as $st): ?>
                <option value="<?= $st ?>" <?= ($ann['status'] ?? '') === $st ? 'selected' : '' ?>><?= announcement_status_label($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Статус на плащане</label>
            <select name="payment_status">
                <?php foreach (['not_required','pending','approved','rejected'] as $ps): ?>
                <option value="<?= $ps ?>" <?= ($ann['payment_status'] ?? '') === $ps ? 'selected' : '' ?>><?= announcement_payment_status_label($ps) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Такса публикуване (€)</label><input name="publish_fee_eur" type="number" step="0.01" value="<?= htmlspecialchars((string)($ann['publish_fee_eur'] ?? '')) ?>"></div>
        <div class="form-group"><label>Референция плащане</label><input name="payment_reference" value="<?= htmlspecialchars($ann['payment_reference'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Админ бележка (плащане)</label><textarea name="payment_admin_notes" rows="2"><?= htmlspecialchars($ann['payment_admin_notes'] ?? '') ?></textarea></div>
    <label><input type="checkbox" name="is_featured" <?= !empty($ann['is_featured']) ? 'checked' : '' ?>> Препоръчана обява</label>
    <p style="margin-top:1rem">
        <button type="submit" class="btn btn-primary">Запази</button>
        <a href="/admin/competition-archive/<?= (int)$ann['id'] ?>" class="btn btn-outline">Отказ</a>
    </p>
</form>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var lat = parseFloat(document.getElementById('lat').value) || 42.6977;
    var lng = parseFloat(document.getElementById('lng').value) || 23.3219;
    var markers = (document.getElementById('lat').value && document.getElementById('lng').value)
        ? [{ lat: lat, lng: lng, label: 'Локация' }] : [];
    initBsMap('ann-map', markers, { pickable: true, latInput: 'lat', lngInput: 'lng', zoom: markers.length ? 12 : 8 });
});
</script>
