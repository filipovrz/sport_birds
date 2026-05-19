<?php /** @var array $ev */ ?>
<tr><th style="width:220px">Заглавие</th><td><?= htmlspecialchars($ev['title']) ?></td></tr>
<tr><th>Вид събитие</th><td><?= event_type_label($ev['event_type'] ?? 'gathering') ?></td></tr>
<tr><th>Публикувал</th><td><?= htmlspecialchars($ev['user_name']) ?> (<?= htmlspecialchars($ev['user_email']) ?>)</td></tr>
<tr><th>Дата</th><td><?= date('d.m.Y', strtotime($ev['event_date'])) ?><?= !empty($ev['event_end_date']) ? ' – ' . date('d.m.Y', strtotime($ev['event_end_date'])) : '' ?></td></tr>
<?php if (!empty($ev['registration_deadline'])): ?><tr><th>Краен срок запис</th><td><?= date('d.m.Y', strtotime($ev['registration_deadline'])) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['location'])): ?><tr><th>Място</th><td><?= htmlspecialchars($ev['location']) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['club_name'])): ?><tr><th>Клуб</th><td><?= htmlspecialchars($ev['club_name']) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['organizer'])): ?><tr><th>Организатор</th><td><?= htmlspecialchars($ev['organizer']) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['description'])): ?><tr><th>Описание</th><td><?= nl2br(htmlspecialchars($ev['description'])) ?></td></tr><?php endif; ?>
<?php if (!empty($ev['attendance_fee_eur'])): ?><tr><th>Такса участие</th><td><?= format_eur((float)$ev['attendance_fee_eur']) ?></td></tr><?php endif; ?>
<tr><th>Статус</th><td><?= announcement_status_label($ev['status']) ?><?= !empty($ev['is_featured']) ? ' · Препоръчано' : '' ?></td></tr>
<tr><th>Плащане публикуване</th><td><?= announcement_payment_status_label($ev['payment_status'] ?? 'not_required') ?></td></tr>
<tr><th>Записани</th><td><?= (int)($ev['reg_count'] ?? 0) ?></td></tr>
<tr><th>Създадена</th><td><?= date('d.m.Y H:i', strtotime($ev['created_at'])) ?></td></tr>
