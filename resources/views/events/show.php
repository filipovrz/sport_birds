<?php if (!empty($isOwner) && ($e['status'] ?? '') !== 'published'): ?>
<div class="alert" style="background:#fff3cd">
    <?php if (($e['payment_status'] ?? '') === 'pending'): ?>
    <p>Обявата чака потвърждение на плащане (<?= format_eur((float)($e['publish_fee_eur'] ?? 0)) ?>).</p>
    <?php elseif (($e['payment_status'] ?? '') === 'rejected'): ?>
    <p>Публикуването е отхвърлено.<?= !empty($e['payment_admin_notes']) ? ' ' . htmlspecialchars($e['payment_admin_notes']) : '' ?></p>
    <?php else: ?>
    <p>Обявата не е публикувана (<?= announcement_status_label($e['status']) ?>).</p>
    <?php endif; ?>
</div>
<?php endif; ?>
<div class="card">
    <p style="margin:0 0 0.5rem;color:var(--muted)"><?= event_type_label($e['event_type'] ?? 'gathering') ?></p>
    <h1><?= htmlspecialchars($e['title']) ?></h1>
    <p><?= date('d.m.Y', strtotime($e['event_date'])) ?><?= !empty($e['event_end_date']) ? ' – ' . date('d.m.Y', strtotime($e['event_end_date'])) : '' ?> · <?= htmlspecialchars($e['location'] ?? '') ?></p>
    <p><?= nl2br(htmlspecialchars($e['description'] ?? '')) ?></p>
    <p><strong>Организатор:</strong> <?= htmlspecialchars($e['organizer'] ?? $e['publisher_name']) ?></p>
    <?php if (!empty($e['attendance_fee_eur'])): ?><p><strong>Такса за участие:</strong> <?= format_eur((float)$e['attendance_fee_eur']) ?></p><?php endif; ?>
</div>
<?php if ($myReg): ?>
<p class="alert alert-success">Вие сте записани за това събитие.</p>
<?php elseif (!empty($canRegister)): ?>
<div class="card">
    <h3>Участие</h3>
    <form method="post" action="/events/<?= (int)$e['id'] ?>/register">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">Ще участвам</button>
        <div class="form-group" style="margin-top:0.75rem"><label>Бележка (по избор)</label><input name="notes" maxlength="500"></div>
    </form>
</div>
<?php elseif (!\App\Core\Auth::check() && ($e['status'] ?? '') === 'published'): ?>
<div class="card"><p><a href="/login" class="btn btn-primary">Ще участвам</a> — влезте в профила си</p></div>
<?php endif; ?>
<div class="card">
    <h3>Участници (<?= count($registrations) ?>)</h3>
    <ul>
    <?php foreach ($registrations as $r): ?>
        <li><?= htmlspecialchars($r['user_name']) ?><?= $r['notes'] ? ' — ' . htmlspecialchars($r['notes']) : '' ?></li>
    <?php endforeach; ?>
    </ul>
</div>
