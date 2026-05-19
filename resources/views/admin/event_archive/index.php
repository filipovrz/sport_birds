<h1>Архив — обяви за събития</h1>
<h2 style="font-size:1.15rem;margin-top:1rem">Активни</h2>
<?php if (empty($active)): ?><div class="card"><p>Няма активни.</p></div><?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Вид</th><th>Дата</th><th>Записани</th><th></th></tr>
<?php foreach ($active as $e): ?>
<tr>
    <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
    <td><?= event_type_label($e['event_type'] ?? 'gathering') ?></td>
    <td><?= date('d.m.Y', strtotime($e['event_date'])) ?></td>
    <td><?= (int)$e['reg_count'] ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/event-archive/<?= (int)$e['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <a href="/admin/event-archive/<?= (int)$e['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
        <?php $deleteAction = '/admin/event-archive/' . (int)$e['id'] . '/delete'; $deleteConfirm = 'Изтриване на „' . $e['title'] . '“?'; require BASE_PATH . '/resources/views/admin/_delete_form.php'; ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
<h2 style="font-size:1.15rem;margin-top:2rem">Архив</h2>
<?php if (empty($archive)): ?><div class="card"><p>Архивът е празен.</p></div><?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Дата</th><th>Статус</th><th></th></tr>
<?php foreach ($archive as $e): ?>
<tr>
    <td><?= htmlspecialchars($e['title']) ?></td>
    <td><?= date('d.m.Y', strtotime($e['event_date'])) ?></td>
    <td><?= announcement_status_label($e['status']) ?></td>
    <td>
        <a href="/admin/event-archive/<?= (int)$e['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <a href="/events/<?= (int)$e['id'] ?>" class="btn btn-outline btn-sm" target="_blank">Публично</a>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
