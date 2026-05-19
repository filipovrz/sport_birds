<?php /** @var array $ann */ ?>
<tr><th style="width:220px">Заглавие</th><td><?= htmlspecialchars($ann['title']) ?></td></tr>
<tr><th>Публикувал</th><td><?= htmlspecialchars($ann['user_name']) ?> (<?= htmlspecialchars($ann['user_email']) ?>)</td></tr>
<tr><th>Тип</th><td><?= competition_type_label($ann['competition_type'] ?? 'race') ?></td></tr>
<tr><th>Вид</th><td><?= competition_species_label($ann['species'] ?? 'racing_pigeon') ?></td></tr>
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
<?php if (!empty($ann['max_participants'])): ?>
<tr><th>Макс. участници</th><td><?= (int)$ann['max_participants'] ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['entry_fee_bgn'])): ?>
<tr><th>Такса за участие</th><td><?= format_eur((float)$ann['entry_fee_bgn']) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['contact_email']) || !empty($ann['contact_phone'])): ?>
<tr><th>Контакт</th><td>
    <?= !empty($ann['contact_email']) ? htmlspecialchars($ann['contact_email']) : '' ?>
    <?= !empty($ann['contact_phone']) ? ' · ' . htmlspecialchars($ann['contact_phone']) : '' ?>
</td></tr>
<?php endif; ?>
<tr><th>Статус</th><td><?= announcement_status_label($ann['status']) ?><?= !empty($ann['is_featured']) ? ' · Препоръчана' : '' ?></td></tr>
<tr><th>Плащане</th><td><?= announcement_payment_status_label($ann['payment_status'] ?? 'not_required') ?><?= !empty($ann['publish_fee_eur']) ? ' — ' . format_eur((float)$ann['publish_fee_eur']) : '' ?></td></tr>
<?php if (!empty($ann['payment_reference'])): ?>
<tr><th>Референция</th><td><?= htmlspecialchars($ann['payment_reference']) ?></td></tr>
<?php endif; ?>
<tr><th>Записани</th><td><?= (int)($ann['reg_count'] ?? 0) ?></td></tr>
<tr><th>Създадена</th><td><?= date('d.m.Y H:i', strtotime($ann['created_at'])) ?></td></tr>
<?php if (!empty($ann['updated_at']) && $ann['updated_at'] !== $ann['created_at']): ?>
<tr><th>Обновена</th><td><?= date('d.m.Y H:i', strtotime($ann['updated_at'])) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['payment_processed_at'])): ?>
<tr><th>Плащане обработено</th><td><?= date('d.m.Y H:i', strtotime($ann['payment_processed_at'])) ?></td></tr>
<?php endif; ?>
<?php if (!empty($ann['payment_admin_notes'])): ?>
<tr><th>Админ бележка (плащане)</th><td><?= nl2br(htmlspecialchars($ann['payment_admin_notes'])) ?></td></tr>
<?php endif; ?>
