<?php if (!empty($isOwner) && ($a['status'] ?? '') !== 'published'): ?>
<div class="alert" style="background:#fff3cd">
    <?php if (($a['payment_status'] ?? '') === 'pending'): ?>
    <p>Обявата чака потвърждение на плащане (<?= format_eur((float)($a['publish_fee_eur'] ?? 0)) ?>) от администратор.</p>
    <?php elseif (($a['payment_status'] ?? '') === 'rejected'): ?>
    <p>Публикуването е отхвърлено.<?= !empty($a['payment_admin_notes']) ? ' ' . htmlspecialchars($a['payment_admin_notes']) : '' ?></p>
    <?php else: ?>
    <p>Обявата не е публикувана (<?= announcement_status_label($a['status']) ?>).</p>
    <?php endif; ?>
</div>
<?php endif; ?>
<div class="card">
    <h1><?= htmlspecialchars($a['title']) ?></h1>
    <p><?= htmlspecialchars($a['event_date']) ?> · <?= htmlspecialchars($a['location'] ?? '') ?> · <?= htmlspecialchars($a['distance_km'] ?? '') ?> км</p>
    <p><?= nl2br(htmlspecialchars($a['description'] ?? '')) ?></p>
    <p><strong>Организатор:</strong> <?= htmlspecialchars($a['organizer'] ?? $a['publisher_name']) ?></p>
    <?php if ($a['entry_fee_bgn']): ?><p><strong>Такса:</strong> <?= format_eur((float)$a['entry_fee_bgn']) ?></p><?php endif; ?>
</div>
<?php if ($myReg): ?>
<p class="alert alert-success">Вие сте записани за това състезание.</p>
<?php elseif (!empty($canRegister)): ?>
<div class="card">
    <h3>Участие</h3>
    <form method="post" action="/announcements/<?= (int)$a['id'] ?>/register">
        <?= csrf_field() ?>
        <p style="margin-bottom:1rem">
            <button type="submit" class="btn btn-primary">Ще участвам</button>
        </p>
        <details>
            <summary style="cursor:pointer;color:var(--primary)">По избор: птица и бележка</summary>
            <div class="form-group" style="margin-top:0.75rem"><label>Птица</label>
                <select name="bird_id"><option value="">— без конкретна птица —</option>
                <?php foreach ($birds as $b): ?><option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['ring_number']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Бележка</label><input name="notes" maxlength="500"></div>
        </details>
    </form>
</div>
<?php elseif (!\App\Core\Auth::check() && ($a['status'] ?? '') === 'published'): ?>
<div class="card">
    <p><a href="/login" class="btn btn-primary">Ще участвам</a> <span style="color:var(--muted);font-size:0.9rem">— влезте в профила си, за да се запишете</span></p>
</div>
<?php endif; ?>
<div class="card">
    <h3>Участници (<?= count($registrations) ?>)</h3>
    <ul>
    <?php foreach ($registrations as $r): ?>
        <li><?= htmlspecialchars($r['user_name']) ?><?= $r['ring_number'] ? ' — '.htmlspecialchars($r['ring_number']) : '' ?></li>
    <?php endforeach; ?>
    </ul>
</div>
