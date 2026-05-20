<?php
use App\Core\Auth;
use App\Services\PaymentMethodsService;
?>
<div class="payment-page">
    <p><a href="/payment-methods">← Всички начини на плащане</a></p>
    <h1><?= htmlspecialchars($method['label']) ?></h1>
    <p class="payment-page__lead">Време: <strong><?= htmlspecialchars($method['timing']) ?></strong></p>

    <?php if (empty($method['active']) && $method['slug'] !== 'bank'): ?>
    <p class="alert" style="background:#fff3cd">Конфигурирайте ключа в <a href="/admin/settings">Админ → Настройки → Онлайн плащания</a>.</p>
    <?php endif; ?>

    <?php if (!empty($pendingPayments)): ?>
    <div class="card" style="margin-bottom:1rem">
        <h3 style="margin-top:0">Незавършени плащания</h3>
        <?php foreach ($pendingPayments as $p): ?>
        <p style="margin:0.5rem 0">
            <?= htmlspecialchars($p['description'] ?? '') ?> — <?= format_eur((float)$p['amount_eur']) ?>
            <a href="/payment/checkout/<?= htmlspecialchars($method['slug']) ?>?payment_token=<?= urlencode($p['public_token']) ?>" class="btn btn-primary btn-sm">Плати сега</a>
        </p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (Auth::check()): ?>
    <div class="card payment-detail">
        <h3 style="margin-top:0">Започни плащане</h3>
        <form method="post" action="<?= htmlspecialchars($checkoutUrl) ?>">
            <?= csrf_field() ?>
            <?php if ($method['slug'] === 'bank' && $bankInstructions !== ''): ?>
            <motion class="payment-detail__bank" style="margin-bottom:1rem"><?= nl2br(htmlspecialchars($bankInstructions)) ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label>Абонаментен план (по избор)</label>
                <select name="plan_id">
                    <option value="0">— Само ако имате незавършена заявка / обява —</option>
                    <?php foreach ($plans as $plan): if (($plan['slug'] ?? '') === 'free') continue; ?>
                    <option value="<?= (int)$plan['id'] ?>"><?= htmlspecialchars($plan['name']) ?> — <?= format_plan_price($plan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Продължи към плащане</button>
        </form>
        <p style="margin-top:1rem;font-size:0.9rem;color:var(--muted)">
            Обява: <a href="/dashboard/announcements/create">състезание</a> ·
            <a href="/dashboard/events/create">събитие</a> (плащането стартира след изпращане)
        </p>
    </div>
    <?php else: ?>
    <p><a href="/login?redirect=<?= urlencode(PaymentMethodsService::methodUrl($method['slug'])) ?>" class="btn btn-primary">Вход за плащане</a></p>
    <?php endif; ?>
</div>
