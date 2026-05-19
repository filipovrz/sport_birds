<h1>Развъждане</h1>
<a href="/dashboard/breeding/create" class="btn btn-primary">+ Двойка</a>
<div class="card"><table>
<tr><th>Сезон</th><th>Мъжки</th><th>Женски</th><th></th></tr>
<?php foreach ($pairs as $p): ?>
<tr><td><?= (int)$p['season_year'] ?></td><td><?= htmlspecialchars($p['male_ring']) ?></td><td><?= htmlspecialchars($p['female_ring']) ?></td>
<td><a href="/dashboard/breeding/<?= (int)$p['id'] ?>">Детайли</a></td></tr>
<?php endforeach; ?>
</table></div>
