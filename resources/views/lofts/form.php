<h1><?= $loft ? 'Редакция на гълъбарник' : 'Нов гълъбарник' ?></h1>
<div class="card">
<form method="post" action="<?= $loft ? '/dashboard/lofts/'.$loft['id'] : '/dashboard/lofts' ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име *</label><input name="name" required value="<?= htmlspecialchars($loft['name'] ?? '') ?>"></div>
    <div class="form-group"><label>Локация (адрес)</label><input name="location" value="<?= htmlspecialchars($loft['location'] ?? '') ?>"></div>
    <input type="hidden" name="latitude" id="lat" value="<?= htmlspecialchars((string)($loft['latitude'] ?? '')) ?>">
    <input type="hidden" name="longitude" id="lng" value="<?= htmlspecialchars((string)($loft['longitude'] ?? '')) ?>">
    <p>Кликнете на картата за GPS позиция на гълъбарника (можете да влачите маркера):</p>
    <p id="loft-coords-hint" style="color:var(--muted);margin-bottom:0.5rem">Кликнете на картата, за да изберете позиция.</p>
    <div id="loft-map" style="height:300px;border-radius:8px;margin-bottom:1rem;z-index:1"></div>
    <div class="form-group"><label>Капацитет</label><input type="number" name="capacity" value="<?= htmlspecialchars((string)($loft['capacity'] ?? '')) ?>"></div>
    <div class="form-group"><label>Бележки</label><textarea name="notes"><?= htmlspecialchars($loft['notes'] ?? '') ?></textarea></div>
    <label style="display:block;margin-top:0.75rem"><input type="checkbox" name="is_public" <?= !empty($loft['is_public']) || (!$loft && !empty(\App\Core\Auth::user()['default_public_lofts'])) ? 'checked' : '' ?>> <strong>Публичен гълъбарник</strong></label>
    <button class="btn btn-primary">Запази</button>
</form>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateLoftCoordsHint() {
        var lat = document.getElementById('lat').value;
        var lng = document.getElementById('lng').value;
        var hint = document.getElementById('loft-coords-hint');
        if (lat && lng) {
            hint.textContent = 'Избрана позиция: ' + lat + ', ' + lng;
            hint.style.color = 'var(--success, #2d8a5e)';
        } else {
            hint.textContent = 'Кликнете на картата, за да изберете позиция.';
            hint.style.color = 'var(--muted)';
        }
    }
    initBsMap('loft-map', [], {
        pickable: true,
        latInput: 'lat',
        lngInput: 'lng',
        zoom: <?= !empty($loft['latitude']) && !empty($loft['longitude']) ? 14 : 8 ?>,
        onPick: updateLoftCoordsHint
    });
    updateLoftCoordsHint();
});
</script>
