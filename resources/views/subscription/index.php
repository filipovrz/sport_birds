<h1>Абонамент</h1>
<p style="color:var(--muted)">Платените планове са месечни, цените са в евро (€).</p>
<?php if ($isPremium): ?>
<p class="alert alert-success">Активен план: <strong><?= htmlspecialchars($current['name'] ?? '') ?></strong>
    (<?= format_eur((float)($current['price_eur'] ?? 0)) ?>)</p>
<?php endif; ?>
<?php if ($pending): ?>
<p class="alert" style="background:#fff3cd">Имате заявка, която чака одобрение — <?= htmlspecialchars($pendingPlanName ?? '') ?>.</p>
<?php endif; ?>
<?php if (!empty($activePlanPrice) && $activePlanPrice > 0): ?>
<p style="color:var(--muted);font-size:0.95rem">Надграждане: можете да заявите само план по-скъп от текущия.</p>
<?php endif; ?>
<div class="grid grid-3">
<?php foreach ($plans as $plan): if ($plan['slug'] === 'free') continue;
    $blockReason = \App\Services\SubscriptionService::planRequestBlockReason(\App\Core\Auth::user(), $plan);
?>
<div class="card stat-card">
<h3><?= htmlspecialchars($plan['name']) ?></h3>
<p class="num"><?= format_plan_price($plan) ?><?= format_plan_price_suffix($plan) ?></p>
<p style="color:var(--muted)"><?= format_plan_period($plan) ?></p>
<p><?= htmlspecialchars($plan['description'] ?? '') ?></p>
<?php if ($blockReason !== null): ?>
<p style="color:var(--muted);font-size:0.9rem"><?= htmlspecialchars($blockReason) ?></p>
<?php else: ?>
<form method="post" action="/dashboard/subscription/request">
    <?= csrf_field() ?>
    <input type="hidden" name="plan_id" value="<?= (int)$plan['id'] ?>">
    <?php require BASE_PATH . '/resources/views/payment/_methods.php'; ?>
    <button class="btn btn-primary" style="margin-top:0.75rem">Продължи към плащане</button>
</form>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<?php if ($paymentInstructions): ?><div class="card"><h3>Инструкции за плащане</h3><p><?= nl2br(htmlspecialchars($paymentInstructions)) ?></p></div><?php endif; ?>
