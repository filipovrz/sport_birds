<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem">
    <h1>GPS устройства</h1>
    <div>
        <a href="/dashboard/map" class="btn btn-outline btn-sm">Карта</a>
        <a href="/dashboard/gps/create" class="btn btn-primary">+ Регистрирай устройство</a>
    </div>
</div>
<div class="card">
<p style="color:var(--muted);font-size:0.9rem">Устройствата са ваша собственост. Сайтът приема координати само от регистрирани устройства с API токен.</p>
<table>
<tr><th>Име</th><th>Сериен №</th><th>Птица</th><th>Последен сигнал</th><th>Статус</th><th></th></tr>
<?php foreach ($devices as $d): ?>
<tr>
    <td><?= htmlspecialchars($d['name']) ?></td>
    <td><code><?= htmlspecialchars($d['serial_number']) ?></code></td>
    <td><?= htmlspecialchars($d['ring_number'] ?? '—') ?></td>
    <td><?= $d['last_seen_at'] ? htmlspecialchars($d['last_seen_at']) : '—' ?></td>
    <td><?= $d['is_active'] ? 'Активен' : 'Изключен' ?></td>
    <td><a href="/dashboard/gps/<?= (int)$d['id'] ?>">Детайли</a></td>
</tr>
<?php endforeach; ?>
<?php if (empty($devices)): ?><tr><td colspan="6">Няма регистрирани GPS устройства.</td></tr><?php endif; ?>
</table>
</div>
