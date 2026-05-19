<h1>Развъдна двойка — сезон <?= (int)$pair['season_year'] ?></h1>
<div class="card">
<?php if (!empty($pair['male_ring'])): ?>
<p><strong>Мъжки:</strong> <?= htmlspecialchars($pair['male_ring']) ?> · <strong>Женски:</strong> <?= htmlspecialchars($pair['female_ring']) ?></p>
<?php endif; ?>
<?php if ($pair['paired_at']): ?><p>Съединени: <?= htmlspecialchars($pair['paired_at']) ?></p><?php endif; ?>
<h3>Клонки</h3>
<?php if (empty($clutches)): ?><p>Няма записани клонки.</p>
<?php else: foreach ($clutches as $c): ?>
<p>Яйца: <?= htmlspecialchars($c['laid_at'] ?? '—') ?> — <?= (int)($c['egg_count'] ?? 0) ?> бр., излюпени: <?= (int)($c['hatched_count'] ?? 0) ?></p>
<?php endforeach; endif; ?>
<a href="/dashboard/breeding" class="btn btn-outline btn-sm">← Назад</a>
</div>
