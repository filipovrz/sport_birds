<h1><?= htmlspecialchars($device['name']) ?></h1>
<div class="card grid grid-2">
    <div>
        <p><strong>Сериен номер:</strong> <code><?= htmlspecialchars($device['serial_number']) ?></code></p>
        <p><strong>Птица:</strong> <?= htmlspecialchars($device['ring_number'] ?? 'Не е свързана') ?></p>
        <p><strong>Последна позиция:</strong>
            <?php if ($device['last_latitude']): ?>
                <?= $device['last_latitude'] ?>, <?= $device['last_longitude'] ?> (<?= htmlspecialchars($device['last_seen_at'] ?? '') ?>)
            <?php else: ?>—<?php endif; ?>
        </p>
        <p><strong>API токен</strong> (за устройството):</p>
        <p><code style="word-break:break-all;font-size:0.85rem"><?= htmlspecialchars($device['api_token']) ?></code></p>
        <form method="post" action="/dashboard/gps/<?= (int)$device['id'] ?>/token" style="display:inline">
            <?= csrf_field() ?>
            <button class="btn btn-outline btn-sm">Нов токен</button>
        </form>
    </div>
    <div>
        <h3>API за устройството</h3>
        <p style="font-size:0.9rem"><code>POST <?= htmlspecialchars($config['url'] ?? '') ?>/api/gps/track</code></p>
        <pre style="background:#f4f7fb;padding:0.75rem;border-radius:8px;font-size:0.8rem">{
  "token": "ВАШИЯТ_ТОКЕН",
  "latitude": 42.6977,
  "longitude": 23.3219,
  "speed_kmh": 45,
  "battery": 85
}</pre>
    </div>
</div>
<?php if ($device['last_latitude']): ?>
<div class="card" style="padding:0;overflow:hidden;margin-top:1rem">
    <div id="gps-track-map" style="height:360px"></div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const markers = <?= json_encode(array_map(fn($h) => [
        'lat' => (float)$h['latitude'],
        'lng' => (float)$h['longitude'],
        'title' => $h['recorded_at'],
        'desc' => '',
    ], $history), JSON_UNESCAPED_UNICODE) ?>;
    if (!markers.length && <?= (float)$device['last_latitude'] ?>) {
        markers.push({ lat: <?= (float)$device['last_latitude'] ?>, lng: <?= (float)$device['last_longitude'] ?>, title: 'Последна', desc: '' });
    }
    initBsMap('gps-track-map', markers, { center: [<?= (float)$device['last_latitude'] ?>, <?= (float)$device['last_longitude'] ?>], zoom: 12 });
});
</script>
<?php endif; ?>
<p style="margin-top:1rem">
    <a href="/dashboard/gps/<?= (int)$device['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
    <form method="post" action="/dashboard/gps/<?= (int)$device['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Изтриване?')">
        <?= csrf_field() ?><button class="btn btn-danger btn-sm">Изтрий</button>
    </form>
</p>
