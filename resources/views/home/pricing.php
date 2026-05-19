<h1>Цени и планове</h1>
<p style="color:var(--muted)">Платените планове са в евро (€) с <strong>месечен абонамент</strong>. Безплатният план е без срок.</p>
<div class="grid grid-3">
<?php foreach ($plans as $plan): ?>
<div class="card stat-card<?= $plan['slug'] === 'popular' ? ' featured' : '' ?>"<?= $plan['slug'] === 'popular' ? ' style="border:2px solid var(--accent)"' : '' ?>>
<h3><?= htmlspecialchars($plan['name']) ?></h3>
<p class="num"><?= format_plan_price($plan) ?><?= format_plan_price_suffix($plan) ?></p>
<?php if (!is_free_plan($plan)): ?><p style="font-size:0.85rem;color:var(--muted)"><?= format_plan_period($plan) ?></p><?php endif; ?>
<p><?= htmlspecialchars($plan['description'] ?? '') ?></p>
<?php if ($plan['max_birds']): ?><p><small>До <?= (int)$plan['max_birds'] ?> птици</small></p><?php else: ?><p><small>Неограничени птици</small></p><?php endif; ?>
<a href="/register" class="btn btn-primary">Регистрация</a>
</div>
<?php endforeach; ?>
</div>
