<div class="card">
    <h1><?= htmlspecialchars($a['title']) ?></h1>
    <p><?= htmlspecialchars($a['event_date']) ?> · <?= htmlspecialchars($a['location'] ?? '') ?> · <?= htmlspecialchars($a['distance_km'] ?? '') ?> км</p>
    <p><?= nl2br(htmlspecialchars($a['description'] ?? '')) ?></p>
    <p><strong>Организатор:</strong> <?= htmlspecialchars($a['organizer'] ?? $a['publisher_name']) ?></p>
    <?php if ($a['entry_fee_bgn']): ?><p><strong>Такса:</strong> <?= $a['entry_fee_bgn'] ?> лв.</p><?php endif; ?>
</div>
<?php if (\App\Core\Auth::check() && !$myReg): ?>
<div class="card">
    <h3>Записване</h3>
    <form method="post" action="/dashboard/announcements/<?= (int)$a['id'] ?>/register">
        <?= csrf_field() ?>
        <div class="form-group"><label>Птица (по избор)</label>
            <select name="bird_id"><option value="">—</option>
            <?php foreach ($birds as $b): ?><option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['ring_number']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary">Запиши се</button>
    </form>
</div>
<?php elseif ($myReg): ?><p class="alert alert-success">Вие сте записани за това състезание.</p><?php endif; ?>
<div class="card">
    <h3>Участници (<?= count($registrations) ?>)</h3>
    <ul>
    <?php foreach ($registrations as $r): ?>
        <li><?= htmlspecialchars($r['user_name']) ?><?= $r['ring_number'] ? ' — '.htmlspecialchars($r['ring_number']) : '' ?></li>
    <?php endforeach; ?>
    </ul>
</div>
