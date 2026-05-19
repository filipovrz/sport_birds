<h1>Нова обява за състезание</h1>
<div class="card">
<form method="post" action="/dashboard/announcements">
    <?= csrf_field() ?>
    <div class="form-group"><label>Заглавие *</label><input name="title" required></div>
    <div class="form-group"><label>Описание</label><textarea name="description"></textarea></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Дата *</label><input type="date" name="event_date" required></div>
        <div class="form-group"><label>Краен срок за запис</label><input type="date" name="registration_deadline"></div>
        <div class="form-group"><label>Място</label><input name="location"></div>
        <div class="form-group"><label>Дистанция (км)</label><input name="distance_km" type="number" step="0.1"></div>
    </div>
    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lng">
    <p>Кликнете на картата за локация на състезанието:</p>
    <div id="ann-map" style="height:280px;border-radius:8px;margin-bottom:1rem"></div>
    <div class="form-group"><label>Клуб / организатор</label><input name="club_name"></div>
    <div class="form-group"><label>Такса (лв)</label><input name="entry_fee_bgn" type="number" step="0.01"></div>
    <label><input type="checkbox" name="is_featured"> Препоръчана обява</label>
    <p style="margin-top:1rem"><button class="btn btn-primary">Публикувай</button></p>
</form>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initBsMap('ann-map', [], { pickable: true, latInput: 'lat', lngInput: 'lng', zoom: 8 });
});
</script>
