<h1>Карта</h1>
<p style="color:var(--muted)">Гълъбарници, GPS позиции, публикувани обяви за състезания и събития.</p>
<div class="card" style="padding:0;overflow:hidden">
    <div id="bs-map" class="map-viewport"></div>
</div>
<p style="margin-top:0.75rem">
    <span class="badge" style="background:#1e5f8a;color:#fff">Гълъбарник</span>
    <span class="badge">GPS птица</span>
    <span class="badge" style="background:#2d8a5e;color:#fff">Състезание</span>
    <span class="badge" style="background:#7c3aed;color:#fff">Събитие</span>
</p>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initBsMap('bs-map', <?= json_encode($markers, JSON_UNESCAPED_UNICODE) ?>, { zoom: 8 });
});
</script>
