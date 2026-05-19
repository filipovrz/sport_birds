<h1>Абонамент</h1>
<?php if ($isPremium): ?><p class="alert alert-success">Активен план: <strong><?= htmlspecialchars($current['name'] ?? '') ?></strong></p><?php endif; ?>
<?php if ($pending): ?><p class="alert" style="background:#fff3cd">Чака одобрение за план #<?= (int)$pending['plan_id'] ?>.</p><?php endif; ?>
<div class="grid grid-3">
<?php foreach ($plans as $plan): if ($plan['slug'] === 'free') continue; ?>
<div class="card stat-card">
<h3><?= htmlspecialchars($plan['name']) ?></h3>
<p class="num"><?= number_format((float)$plan['price_bgn'], 2) ?> лв / <?= (int)$plan['duration_days'] ?> дни</p>
<p><?= htmlspecialchars($plan['description'] ?? '') ?></p>
<form method="post" action="/dashboard/subscription/request">
    <?= csrf_field() ?>
<input type="hidden" name="plan_id" value="<?= (int)$plan['id'] ?>">
<input name="payment_reference" placeholder="Референция на плащане">
<button class="btn btn-primary">Заяви</button>
</form>
</div>
<?php endforeach; ?>
</div>
<?php if ($paymentInstructions): ?><div class="card"><h3>Инструкции за плащане</h3><p><?= nl2br(htmlspecialchars($paymentInstructions)) ?></p></div><?php endif; ?>
