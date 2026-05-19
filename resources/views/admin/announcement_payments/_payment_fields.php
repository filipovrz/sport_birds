<?php /** @var array $ann */ ?>
<tr><th style="width:220px">Заглавие</th><td><?= htmlspecialchars($ann['title']) ?></td></tr>
<tr><th>Потребител</th><td><?= htmlspecialchars($ann['user_name']) ?></td></tr>
<tr><th>Имейл</th><td><?= htmlspecialchars($ann['email']) ?></td></tr>
<?php if (!empty($ann['phone'])): ?>
<tr><th>Телефон</th><td><?= htmlspecialchars($ann['phone']) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['city'])): ?>
<tr><th>Град</th><td><?= htmlspecialchars($ann['city']) ?></td></tr>
<?php endif; ?>
<tr><th>Дата на събитие</th><td><?= date('d.m.Y', strtotime($ann['event_date'])) ?></td></tr>
<?php if (!empty($ann['registration_deadline'])): ?>
<tr><th>Краен срок за запис</th><td><?= date('d.m.Y', strtotime($ann['registration_deadline'])) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['location'])): ?>
<tr><th>Място</th><td><?= htmlspecialchars($ann['location']) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['distance_km'])): ?>
<tr><th>Дистанция</th><td><?= htmlspecialchars((string)$ann['distance_km']) ?> км</td></tr>
<?php endif; ?>
<?php if (!empty($ann['club_name'])): ?>
<tr><th>Клуб</th><td><?= htmlspecialchars($ann['club_name']) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['organizer'])): ?>
<tr><th>Организатор</th><td><?= htmlspecialchars($ann['organizer']) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['description'])): ?>
<tr><th>Описание</th><td><?= nl2br(htmlspecialchars($ann['description'])) ?></td></tr>
<?php endif; ?>
<tr><th>Такса за публикуване</th><td><?= format_eur((float)($ann['publish_fee_eur'] ?? 0)) ?></td></tr>
<tr><th>Референция на плащане</th><td><?= htmlspecialchars($ann['payment_reference'] ?? '—') ?></td></tr>
<tr><th>Статус на плащане</th><td><?= announcement_payment_status_label($ann['payment_status']) ?></td></tr>
<tr><th>Статус на обявата</th><td><?= announcement_status_label($ann['status']) ?><?= !empty($ann['is_featured']) ? ' · Препоръчана' : '' ?></td></tr>
<tr><th>Подадена на</th><td><?= date('d.m.Y H:i', strtotime($ann['created_at'])) ?></td></tr>
<?php if (!empty($ann['payment_processed_at'])): ?>
<tr><th>Обработена на</th><td><?= date('d.m.Y H:i', strtotime($ann['payment_processed_at'])) ?><?= !empty($ann['processed_by_name']) ? ' — ' . htmlspecialchars($ann['processed_by_name']) : '' ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['payment_admin_notes'])): ?>
<tr><th>Админ бележка</th><td><?= nl2br(htmlspecialchars($ann['payment_admin_notes'])) ?></td></tr>
<?php endif; ?>
