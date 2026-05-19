<h1>Цени и планове</h1>
<div class="grid grid-3">
<?php foreach ($plans as $plan): ?>
<div class="card stat-card">
<h3><?= htmlspecialchars($plan['name']) ?></h3>
<p class="num"><?= number_format((float)$plan['price_bgn'], 2) ?> лв.</p>
<p><?= htmlspecialchars($plan['description'] ?? '') ?></p>
<a href="/register" class="btn btn-primary">Регистрация</a>
</div>
<?php endforeach; ?>
</div>
