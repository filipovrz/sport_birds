<?php /** @var array $ev */ ?>
<tr><th style="width:220px">Заглавие</th><td><?= htmlspecialchars($ev['title']) ?></td></tr>
<tr><th>Вид</th><td><?= event_type_label($ev['event_type'] ?? 'gathering') ?></td></tr>
<tr><th>Потребител</th><td><?= htmlspecialchars($ev['user_name']) ?></td></tr>
<tr><th>Имейл</th><td><?= htmlspecialchars($ev['email']) ?></td></tr>
<tr><th>Дата</th><td><?= date('d.m.Y', strtotime($ev['event_date'])) ?></td></tr>
<?php if (!empty($ev['location'])): ?><tr><th>Място</th><td><?= htmlspecialchars($ev['location']) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['description'])): ?><tr><th>Описание</th><td><?= nl2br(htmlspecialchars($ev['description'])) ?></td></tr><?php endif; ?>
<tr><th>Такса публикуване</th><td><?= format_eur((float)($ev['publish_fee_eur'] ?? 0)) ?></td></tr>
<tr><th>Референция</th><td><?= htmlspecialchars($ev['payment_reference'] ?? '—') ?></td></tr>
<tr><th>Плащане</th><td><?= announcement_payment_status_label($ev['payment_status']) ?></td></tr>
<tr><th>Обява</th><td><?= announcement_status_label($ev['status']) ?></td></tr>
<tr><th>Подадена</th><td><?= date('d.m.Y H:i', strtotime($ev['created_at'])) ?></td></tr>
<?php if (!empty($ev['payment_processed_at'])): ?>
<tr><th>Обработена</th><td><?= date('d.m.Y H:i', strtotime($ev['payment_processed_at'])) ?><?= !empty($ev['processed_by_name']) ? ' — ' . htmlspecialchars($ev['processed_by_name']) : '' ?></td></tr>
<?php endif; ?>
<?php if (!empty($ev['payment_admin_notes'])): ?>
<tr><th>Админ бележка</th><td><?= nl2br(htmlspecialchars($ev['payment_admin_notes'])) ?></td></tr>
<?php endif; ?>
