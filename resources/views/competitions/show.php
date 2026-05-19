<h1><?= htmlspecialchars($competition['name']) ?></h1>
<div class="card">
<h3>Резултати</h3>
<table><tr><th>Място</th><th>Птица</th><th>Скорост</th><th>Точки</th></tr>
<?php foreach ($results as $r): ?>
<tr><td><?= (int)($r['position'] ?? 0) ?></td><td><?= htmlspecialchars($r['ring_number']) ?></td>
<td><?= htmlspecialchars((string)($r['velocity_mpm'] ?? '—')) ?></td><td><?= htmlspecialchars((string)($r['points'] ?? '—')) ?></td></tr>
<?php endforeach; ?>
</table>
<h3>Добави резултат</h3>
<form method="post" action="/dashboard/competitions/<?= (int)$competition['id'] ?>/results">
    <?= csrf_field() ?>
<select name="bird_id"><?php foreach ($birds as $b): ?><option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['ring_number']) ?></option><?php endforeach; ?></select>
<input name="position" type="number" placeholder="Място">
<input name="velocity_mpm" placeholder="Скорост m/min">
<button class="btn btn-primary btn-sm">Добави</button>
</form>
</div>
