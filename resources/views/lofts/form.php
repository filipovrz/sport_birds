<h1><?= $loft ? 'Редакция на птичарник' : 'Нов птичарник' ?></h1>
<div class="card">
<form method="post" action="<?= $loft ? '/dashboard/lofts/'.$loft['id'] : '/dashboard/lofts' ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име *</label><input name="name" required value="<?= htmlspecialchars($loft['name'] ?? '') ?>"></div>
    <div class="form-group"><label>Локация (адрес)</label><input name="location" value="<?= htmlspecialchars($loft['location'] ?? '') ?>"></div>
    <input type="hidden" name="latitude" id="lat" value="<?= htmlspecialchars((string)($loft['latitude'] ?? '')) ?>">
    <input type="hidden" name="longitude" id="lng" value="<?= htmlspecialchars((string)($loft['longitude'] ?? '')) ?>">
    <p>Кликнете на картата за GPS позиция на птичарника:</p>
    <div id="loft-map" style="height:300px;border-radius:8px;margin-bottom:1rem"></div>
    <div class="form-group"><label>Капацитет</label><input type="number" name="capacity" value="<?= htmlspecialchars((string)($loft['capacity'] ?? '')) ?>"></div>
    <div class="form-group"><label>Бележки</label><textarea name="notes"><?= htmlspecialchars($loft['notes'] ?? '') ?></textarea></div>
    <button class="btn btn-primary">Запази</button>
</form>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initBsMap('loft-map', [], { pickable: true, latInput: 'lat', lngInput: 'lng', zoom: <?= ($loft['latitude'] ?? '') ? 14 : 8 ?> });
});
</script>
