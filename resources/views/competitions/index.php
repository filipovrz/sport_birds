<h1>Състезания</h1>
<a href="/dashboard/competitions/create" class="btn btn-primary">+ Състезание</a>
<div class="card"><table>
<tr><th>Име</th><th>Дата</th><th>Тип</th><th></th></tr>
<?php foreach ($competitions as $c): ?>
<tr><td><?= htmlspecialchars($c['name']) ?></td><td><?= htmlspecialchars($c['event_date']) ?></td>
<td><?= competition_type_label($c['competition_type']) ?></td>
<td><a href="/dashboard/competitions/<?= (int)$c['id'] ?>">Резултати</a></td></tr>
<?php endforeach; ?>
</table></div>
