<h1>Моите обяви за събития</h1>
<a href="/dashboard/events/create" class="btn btn-primary">+ Нова обява</a>
<div class="card" style="margin-top:1rem">
<table>
<tr><th>Заглавие</th><th>Вид</th><th>Дата</th><th>Статус</th><th>Плащане</th><th></th></tr>
<?php foreach ($events as $e): ?>
<tr>
    <td><?= htmlspecialchars($e['title']) ?></td>
    <td><?= event_type_label($e['event_type'] ?? 'gathering') ?></td>
    <td><?= date('d.m.Y', strtotime($e['event_date'])) ?></td>
    <td><?= announcement_status_label($e['status']) ?></td>
    <td><?= announcement_payment_status_label($e['payment_status'] ?? 'not_required') ?></td>
    <td><a href="/events/<?= (int)$e['id'] ?>">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
