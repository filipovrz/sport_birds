<h1>Моите обяви</h1>
<a href="/dashboard/announcements/create" class="btn btn-primary">+ Нова обява</a>
<div class="card" style="margin-top:1rem">
<table>
<tr><th>Заглавие</th><th>Дата</th><th>Статус</th><th></th></tr>
<?php foreach ($announcements as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['title']) ?></td>
    <td><?= htmlspecialchars($a['event_date']) ?></td>
    <td><?= htmlspecialchars($a['status']) ?></td>
    <td><a href="/announcements/<?= (int)$a['id'] ?>">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
