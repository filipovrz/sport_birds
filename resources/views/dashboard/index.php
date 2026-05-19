<h1>Табло</h1>
<?php if (!$isPremium): ?><p class="alert" style="background:#fff3cd">Безплатен план — <a href="/dashboard/subscription">надградете</a> за пълен достъп.</p><?php endif; ?>
<div class="grid grid-3">
    <div class="card stat-card"><div class="num"><?= (int)$stats['birds'] ?></div><div>Птици</div></div>
    <div class="card stat-card"><div class="num"><?= (int)$stats['lofts'] ?></div><div>Птицарни</div></div>
    <div class="card stat-card"><div class="num"><?= htmlspecialchars($plan['name'] ?? '—') ?></div><div>Текущ план</div></div>
</div>
<div class="grid grid-2">
    <div class="card">
        <h3>Предстоящи здравни прегледи</h3>
        <?php if (empty($stats['upcoming_health'])): ?><p>Няма записи.</p>
        <?php else: foreach ($stats['upcoming_health'] as $h): ?>
            <p><?= htmlspecialchars($h['title']) ?> — <?= htmlspecialchars($h['next_due_at']) ?></p>
        <?php endforeach; endif; ?>
    </div>
    <div class="card">
        <h3>Последни състезания</h3>
        <?php if (empty($stats['recent_competitions'])): ?><p>Няма записи.</p>
        <?php else: foreach ($stats['recent_competitions'] as $c): ?>
            <p><a href="/dashboard/competitions/<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a> — <?= htmlspecialchars($c['event_date']) ?></p>
        <?php endforeach; endif; ?>
    </div>
</div>
<p><a href="/dashboard/birds/create" class="btn btn-primary">+ Нова птица</a></p>
