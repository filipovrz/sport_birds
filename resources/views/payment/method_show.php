<?php use App\Services\PaymentMethodsService; ?>
<div class="payment-page">
    <p><a href="/payment-methods">← Всички начини на плащане</a></p>
    <h1><?= htmlspecialchars($method['label']) ?></h1>
    <p class="payment-page__lead">Време за обработка: <strong><?= htmlspecialchars($method['timing']) ?></strong></p>

    <div class="card payment-detail">
        <?php if ($method['slug'] === 'bank'): ?>
        <p>След превода посочете референцията <strong>BSB-…</strong> от екрана за плащане. Администраторът активира услугата след потвърждение.</p>
        <?php if ($bankInstructions !== ''): ?>
        <div class="payment-detail__bank"><?= nl2br(htmlspecialchars($bankInstructions)) ?></div>
        <?php else: ?>
        <p style="color:var(--muted)">Банковите реквизити ще бъдат показани при плащане.</p>
        <?php endif; ?>
        <?php elseif (!empty($method['active'])): ?>
        <p>Ще бъдете пренасочени към защитена страница за плащане. Услугата се активира автоматично след успех.</p>
        <?php else: ?>
        <p class="alert" style="background:#fff3cd">Този метод временно не е активиран. Свържете се с администратора или изберете друг метод.</p>
        <?php endif; ?>

        <?php if (!empty($method['active'])): ?>
        <p style="margin-top:1.25rem">
            <a href="<?= htmlspecialchars($continueUrl) ?>" class="btn btn-primary">Продължи с този метод</a>
        </p>
        <?php endif; ?>
        <?php if (\App\Core\Auth::check()): ?>
        <p style="margin-top:0.75rem;font-size:0.9rem;color:var(--muted)">
            Или: <a href="/dashboard/announcements/create">нова обява</a> · <a href="/dashboard/events/create">събитие</a>
        </p>
        <?php endif; ?>
    </div>
</div>
