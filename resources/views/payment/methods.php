<?php use App\Services\PaymentMethodsService; ?>
<div class="payment-page">
    <h1>Начини на плащане</h1>
    <p class="payment-page__lead">Изберете разплащателна система. Плащането се извършва при абонамент или публикуване на обява.</p>

    <div class="payment-grid">
        <?php foreach ($methods as $m): ?>
        <a href="<?= htmlspecialchars(PaymentMethodsService::methodUrl($m['slug'])) ?>" class="payment-card<?= empty($m['active']) ? ' payment-card--inactive' : '' ?>">
            <div class="payment-card__logo payment-card__logo--<?= htmlspecialchars($m['icon']) ?>"><?= payment_icon_text($m['icon']) ?></div>
            <div class="payment-card__name"><?= htmlspecialchars($m['label']) ?></div>
            <div class="payment-card__timing"><?= htmlspecialchars($m['timing']) ?></div>
            <span class="payment-card__btn">Продължи</span>
        </a>
        <?php endforeach; ?>
    </div>

    <p class="payment-page__note">Цените са в евро (€). Онлайн плащанията активират услугата веднага след успех.</p>
</div>
