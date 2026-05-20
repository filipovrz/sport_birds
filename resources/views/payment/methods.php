<?php
use App\Core\Auth;
use App\Services\PaymentMethodsService;
?>
<div class="payment-page">
    <h1>Начини на плащане</h1>
    <p class="payment-page__lead">Изберете разплащателна система.</p>

    <div class="payment-grid">
        <?php foreach ($methods as $m):
            $href = Auth::check()
                ? PaymentMethodsService::checkoutUrl($m['slug'])
                : '/login?redirect=' . urlencode(PaymentMethodsService::methodUrl($m['slug']));
        ?>
        <a href="<?= htmlspecialchars($href) ?>" class="payment-card<?= empty($m['active']) && $m['slug'] !== 'bank' ? ' payment-card--inactive' : '' ?>">
            <div class="payment-card__logo payment-card__logo--<?= htmlspecialchars($m['icon']) ?>"><?= payment_icon_text($m['icon']) ?></div>
            <div class="payment-card__name"><?= htmlspecialchars($m['label']) ?></div>
            <div class="payment-card__timing"><?= htmlspecialchars($m['timing']) ?></div>
            <span class="payment-card__btn">Продължи</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
