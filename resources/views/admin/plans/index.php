<h1>Абонаментни планове <a href="/admin/plans/create" class="btn btn-primary btn-sm">+ Нов план</a></h1>
<p style="color:var(--muted)">Платените планове са в евро (€) с месечен абонамент (30 дни). Плановете могат да се създават, редактират и изтриват оттук.</p>
<div class="card"><table>
<tr><th>Име</th><th>Птици</th><th>Цена</th><th>Период</th><th>Активен</th><th></th></tr>
<?php foreach ($plans as $p): ?>
<tr>
    <td><strong><?= htmlspecialchars($p['name']) ?></strong><br><small style="color:var(--muted)"><?= htmlspecialchars($p['slug']) ?></small></td>
    <td><?= $p['max_birds'] !== null ? 'до ' . (int)$p['max_birds'] : '∞' ?></td>
    <td><?= format_plan_price($p) ?></td>
    <td><?= format_plan_period($p) ?></td>
    <td><?= $p['is_active'] ? 'Да' : 'Не' ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/plans/<?= (int)$p['id'] ?>/edit">Редакция</a>
        <?php if ($p['slug'] !== 'free'): ?>
        <form method="post" action="/admin/plans/<?= (int)$p['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Изтриване на плана?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger btn-sm">Изтрий</button>
        </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
