<h1>Плащания за обяви</h1>
<p style="color:var(--muted)">Текуща такса за публикуване: <strong><?= format_eur($publishFee) ?></strong> · <a href="/admin/settings">Промяна в настройки</a></p>

<h2 style="font-size:1.15rem;margin-top:1.5rem">Чакащи одобрение</h2>
<?php if (empty($announcements)): ?>
<div class="card"><p>Няма чакащи заявки за публикуване.</p></div>
<?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Потребител</th><th>Дата на събитие</th><th>Такса</th><th>Референция</th><th></th></tr>
<?php foreach ($announcements as $a): ?>
<tr>
    <td><strong><?= htmlspecialchars($a['title']) ?></strong><?php if (!empty($a['location'])): ?><br><small><?= htmlspecialchars($a['location']) ?></small><?php endif; ?></td>
    <td><?= htmlspecialchars($a['user_name']) ?></td>
    <td><?= date('d.m.Y', strtotime($a['event_date'])) ?></td>
    <td><?= format_eur((float)($a['publish_fee_eur'] ?? 0)) ?></td>
    <td><?= htmlspecialchars($a['payment_reference'] ?? '—') ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/announcement-payments/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <?php
        $deleteAction = '/admin/announcement-payments/' . (int)$a['id'] . '/delete';
        $deleteConfirm = 'Сигурни ли сте, че искате да изтриете обявата „' . $a['title'] . '“?';
        require BASE_PATH . '/resources/views/admin/_delete_form.php';
        ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>

<h2 style="font-size:1.15rem;margin-top:2rem">История на плащанията</h2>
<?php if (empty($history)): ?>
<div class="card"><p>Все още няма обработени плащания за обяви.</p></div>
<?php else: ?>
<div class="card"><table>
<tr><th>Заглавие</th><th>Потребител</th><th>Такса</th><th>Референция</th><th>Плащане</th><th>Обработено</th><th></th></tr>
<?php foreach ($history as $a): ?>
<tr>
    <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
    <td><?= htmlspecialchars($a['user_name']) ?></td>
    <td><?= format_eur((float)($a['publish_fee_eur'] ?? 0)) ?></td>
    <td><?= htmlspecialchars($a['payment_reference'] ?? '—') ?></td>
    <td><?= announcement_payment_status_label($a['payment_status']) ?></td>
    <td><?= !empty($a['payment_processed_at']) ? date('d.m.Y H:i', strtotime($a['payment_processed_at'])) : '—' ?></td>
    <td style="white-space:nowrap">
        <a href="/admin/announcement-payments/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm">Преглед</a>
        <?php if ($a['payment_status'] === 'approved' && ($a['status'] ?? '') === 'published'): ?>
        <a href="/announcements/<?= (int)$a['id'] ?>" class="btn btn-outline btn-sm" target="_blank">Обява</a>
        <?php endif; ?>
        <?php
        $deleteAction = '/admin/announcement-payments/' . (int)$a['id'] . '/delete';
        $deleteConfirm = 'Сигурни ли сте, че искате да изтриете обявата „' . $a['title'] . '“?';
        require BASE_PATH . '/resources/views/admin/_delete_form.php';
        ?>
    </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
