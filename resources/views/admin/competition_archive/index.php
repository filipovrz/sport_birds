<h1>Архив на състезанията</h1>
<p style="color:var(--muted)">Управление на всички обяви за състезания — активни, предстоящи и минали.</p>

<h2 style="font-size:1.15rem;margin-top:1.5rem">Активни и предстоящи</h2>
<?php if (empty($active)): ?>
<div class="card"><p>Няма активни или предстоящи състезания.</p></div>
<?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Дата</th><th>Публикувал</th><th>Статус</th><th>Записани</th><th></th></tr>
<?php foreach ($active as $a): ?>
<tr>
    <td><strong><?= htmlspecialchars($a['title']) ?></strong><?php if (!empty($a['location'])): ?><br><small><?= htmlspecialchars($a['location']) ?></small><?php endif; ?></td>
    <td><?= date('d.m.Y', strtotime($a['event_date'])) ?></td>
    <td><?= htmlspecialchars($a['user_name']) ?></td>
    <td><?= announcement_status_label($a['status']) ?></td>
    <td><?= (int)$a['reg_count'] ?><?= $a['max_participants'] ? ' / ' . (int)$a['max_participants'] : '' ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/competition-archive/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <a href="/admin/competition-archive/<?= (int)$a['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
        <?php
        $deleteAction = '/admin/competition-archive/' . (int)$a['id'] . '/delete';
        $deleteConfirm = 'Сигурни ли сте, че искате да изтриете „' . $a['title'] . '“?';
        require BASE_PATH . '/resources/views/admin/_delete_form.php';
        ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>

<h2 style="font-size:1.15rem;margin-top:2rem">Архив (минали и приключени)</h2>
<?php if (empty($archive)): ?>
<div class="card"><p>Архивът е празен.</p></div>
<?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Дата</th><th>Публикувал</th><th>Статус</th><th>Записани</th><th></th></tr>
<?php foreach ($archive as $a): ?>
<tr>
    <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
    <td><?= date('d.m.Y', strtotime($a['event_date'])) ?></td>
    <td><?= htmlspecialchars($a['user_name']) ?></td>
    <td><?= announcement_status_label($a['status']) ?></td>
    <td><?= (int)$a['reg_count'] ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/competition-archive/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <a href="/admin/competition-archive/<?= (int)$a['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
        <?php if ($a['status'] === 'published'): ?>
        <a href="/announcements/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm" target="_blank">Обява</a>
        <?php endif; ?>
        <?php
        $deleteAction = '/admin/competition-archive/' . (int)$a['id'] . '/delete';
        $deleteConfirm = 'Сигурни ли сте, че искате да изтриете „' . $a['title'] . '“?';
        require BASE_PATH . '/resources/views/admin/_delete_form.php';
        ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
