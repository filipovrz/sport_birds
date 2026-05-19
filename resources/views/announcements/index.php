<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap">
    <h1>Обяви за състезания</h1>
    <?php if (\App\Core\Auth::check()): ?>
    <div>
        <a href="/dashboard/announcements/create" class="btn btn-primary btn-sm">+ Публикувай</a>
        <a href="/dashboard/announcements/my" class="btn btn-outline btn-sm">Моите обяви</a>
    </div>
    <?php endif; ?>
</div>
<div class="grid grid-2">
<?php foreach ($announcements as $a): ?>
<div class="card">
    <?php if ($a['is_featured']): ?><span class="badge">Препоръчано</span><?php endif; ?>
    <h3><a href="/announcements/<?= (int)$a['id'] ?>"><?= htmlspecialchars($a['title']) ?></a></h3>
    <p><?= htmlspecialchars($a['event_date']) ?> · <?= htmlspecialchars($a['location'] ?? '—') ?></p>
    <p style="color:var(--muted);font-size:0.9rem"><?= htmlspecialchars($a['publisher_name']) ?><?= $a['publisher_club'] ? ' · '.$a['publisher_club'] : '' ?></p>
    <p>Записани: <?= (int)$a['reg_count'] ?><?= $a['max_participants'] ? ' / '.$a['max_participants'] : '' ?></p>
</div>
<?php endforeach; ?>
</div>
<?php if (empty($announcements)): ?><p>Няма активни обяви.</p><?php endif; ?>
