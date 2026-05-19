<h1>Общност — публични профили</h1>
<p style="color:var(--muted)">Разглеждайте птици, гълъбарници и развъждане, маркирани като „Публично“ от други потребители.</p>
<form method="get" action="/community" style="margin-bottom:1.5rem;display:flex;gap:0.5rem;flex-wrap:wrap">
    <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Търсене по име, пръстен, клуб..." style="flex:1;min-width:200px">
    <button type="submit" class="btn btn-primary">Търси</button>
    <?php if ($q !== ''): ?><a href="/community" class="btn btn-outline">Изчисти</a><?php endif; ?>
</form>

<h2 style="font-size:1.1rem">Потребители</h2>
<?php if (empty($users)): ?><p style="color:var(--muted)">Няма публични профили.</p>
<?php else: ?>
<div class="card"><table>
<tr><th>Име</th><th>Град</th><th>Клуб</th><th>Публични птици</th><th></th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= htmlspecialchars($u['city'] ?? '—') ?></td>
    <td><?= htmlspecialchars($u['club_name'] ?? '—') ?></td>
    <td><?= (int)$u['public_birds'] ?></td>
    <td><a href="/community/users/<?= (int)$u['id'] ?>">Профил</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>

<h2 style="font-size:1.1rem;margin-top:1.5rem">Публични птици</h2>
<?php if (empty($birds)): ?><p style="color:var(--muted)">Няма публични птици.</p>
<?php else: ?>
<div class="card"><table>
<tr><th>Пръстен</th><th>Име</th><th>Собственик</th><th></th></tr>
<?php foreach ($birds as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['ring_number']) ?></td>
    <td><?= htmlspecialchars($b['name'] ?? '—') ?></td>
    <td><a href="/community/users/<?= (int)$b['owner_id'] ?>"><?= htmlspecialchars($b['owner_name']) ?></a></td>
    <td><a href="/community/birds/<?= (int)$b['id'] ?>">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>

<h2 style="font-size:1.1rem;margin-top:1.5rem">Публични гълъбарници</h2>
<?php if (empty($lofts)): ?><p style="color:var(--muted)">Няма публични гълъбарници.</p>
<?php else: ?>
<div class="card"><table>
<tr><th>Име</th><th>Място</th><th>Собственик</th><th></th></tr>
<?php foreach ($lofts as $l): ?>
<tr>
    <td><?= htmlspecialchars($l['name']) ?></td>
    <td><?= htmlspecialchars($l['location'] ?? '—') ?></td>
    <td><?= htmlspecialchars($l['owner_name']) ?></td>
    <td><a href="/community/lofts/<?= (int)$l['id'] ?>">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>

<h2 style="font-size:1.1rem;margin-top:1.5rem">Публично развъждане</h2>
<?php if (empty($breeding)): ?><p style="color:var(--muted)">Няма публични развъдни двойки.</p>
<?php else: ?>
<div class="card"><table>
<tr><th>Сезон</th><th>Двойка</th><th>Собственик</th><th></th></tr>
<?php foreach ($breeding as $bp): ?>
<tr>
    <td><?= (int)$bp['season_year'] ?></td>
    <td><?= htmlspecialchars($bp['male_ring']) ?> × <?= htmlspecialchars($bp['female_ring']) ?></td>
    <td><?= htmlspecialchars($bp['owner_name']) ?></td>
    <td><a href="/community/breeding/<?= (int)$bp['id'] ?>">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
