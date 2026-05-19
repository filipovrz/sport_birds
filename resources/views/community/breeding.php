<p><a href="/community">← Общност</a> · <a href="/community/users/<?= (int)$pair['owner_id'] ?>"><?= htmlspecialchars($pair['owner_name']) ?></a></p>
<h1>Развъдна двойка — сезон <?= (int)$pair['season_year'] ?></h1>
<div class="card">
    <p><strong>Собственик:</strong> <?= htmlspecialchars($pair['owner_name']) ?><?= $pair['owner_club'] ? ' · ' . htmlspecialchars($pair['owner_club']) : '' ?></p>
    <p>
        <strong>Мъжки:</strong>
        <?php if (!empty($pair['male_id'])): ?><a href="/community/birds/<?= (int)$pair['male_id'] ?>"><?= htmlspecialchars($pair['male_ring']) ?></a><?php else: ?><?= htmlspecialchars($pair['male_ring']) ?><?php endif; ?>
        · <strong>Женски:</strong>
        <?php if (!empty($pair['female_id'])): ?><a href="/community/birds/<?= (int)$pair['female_id'] ?>"><?= htmlspecialchars($pair['female_ring']) ?></a><?php else: ?><?= htmlspecialchars($pair['female_ring']) ?><?php endif; ?>
    </p>
    <?php if ($pair['paired_at']): ?><p><strong>Съединени:</strong> <?= date('d.m.Y', strtotime($pair['paired_at'])) ?></p><?php endif; ?>
    <?php if ($pair['notes']): ?><p><strong>Бележки:</strong><br><?= nl2br(htmlspecialchars($pair['notes'])) ?></p><?php endif; ?>
    <?php if ($isOwner): ?><p><a href="/dashboard/breeding/<?= (int)$pair['id'] ?>" class="btn btn-outline btn-sm">Моята двойка</a></p><?php endif; ?>
</div>
<?php if (!empty($clutches)): ?>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Клонки</h2>
    <?php foreach ($clutches as $c): ?>
    <p>Яйца: <?= !empty($c['laid_at']) ? date('d.m.Y', strtotime($c['laid_at'])) : '—' ?>
        — <?= (int)($c['egg_count'] ?? 0) ?> бр., излюпени: <?= (int)($c['hatched_count'] ?? 0) ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>
