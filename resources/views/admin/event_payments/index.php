<h1>Плащания — обяви за събития</h1>
<p style="color:var(--muted)">Такса: <strong><?= format_eur($publishFee) ?></strong> (0 € = безплатно) · <a href="/admin/settings">Настройки</a></p>
<h2 style="font-size:1.15rem;margin-top:1.5rem">Чакащи</h2>
<?php if (empty($events)): ?><div class="card"><p>Няма чакащи.</p></div><?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Вид</th><th>Потребител</th><th>Дата</th><th>Такса</th><th></th></tr>
<?php foreach ($events as $e): ?>
<tr>
    <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
    <td><?= event_type_label($e['event_type'] ?? 'gathering') ?></td>
    <td><?= htmlspecialchars($e['user_name']) ?></td>
    <td><?= date('d.m.Y', strtotime($e['event_date'])) ?></td>
    <td><?= format_eur((float)($e['publish_fee_eur'] ?? 0)) ?></td>
    <td><a href="/admin/event-payments/<?= (int)$e['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
    <?php $deleteAction = '/admin/event-payments/' . (int)$e['id'] . '/delete'; $deleteConfirm = 'Изтриване на „' . $e['title'] . '“?'; require BASE_PATH . '/resources/views/admin/_delete_form.php'; ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
<h2 style="font-size:1.15rem;margin-top:2rem">История</h2>
<?php if (empty($history)): ?><div class="card"><p>Няма история.</p></div><?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Такса</th><th>Статус</th><th>Обработено</th><th></th></tr>
<?php foreach ($history as $e): ?>
<tr>
    <td><?= htmlspecialchars($e['title']) ?></td>
    <td><?= format_eur((float)($e['publish_fee_eur'] ?? 0)) ?></td>
    <td><?= announcement_payment_status_label($e['payment_status']) ?></td>
    <td><?= !empty($e['payment_processed_at']) ? date('d.m.Y H:i', strtotime($e['payment_processed_at'])) : '—' ?></td>
    <td><a href="/admin/event-payments/<?= (int)$e['id'] ?>" class="btn btn-outline btn-sm">Преглед</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
