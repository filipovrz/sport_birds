<h1>Развъдна двойка #<?= (int)$pair['id'] ?></h1>
<div class="card">
<p>Сезон: <?= (int)$pair['season_year'] ?></p>
<h3>Клонки</h3>
<?php foreach ($clutches as $c): ?>
<p>Яйца: <?= htmlspecialchars($c['laid_at'] ?? '') ?> — <?= (int)($c['egg_count'] ?? 0) ?> бр.</p>
<?php endforeach; ?>
</div>
