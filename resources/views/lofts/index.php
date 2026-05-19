<div style="display:flex;justify-content:space-between"><h1>Гълъбарници</h1><a href="/dashboard/lofts/create" class="btn btn-primary">+ Добави</a></div>
<div class="card"><table>
<tr><th>Име</th><th>Локация</th><th>Птици</th><th></th></tr>
<?php foreach ($lofts as $l): ?>
<tr><td><?= htmlspecialchars($l['name']) ?></td><td><?= htmlspecialchars($l['location'] ?? '—') ?></td><td><?= (int)$l['bird_count'] ?></td>
<td><a href="/dashboard/lofts/<?= (int)$l['id'] ?>">Преглед</a></td></tr>
<?php endforeach; ?>
</table></div>
