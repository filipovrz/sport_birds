<h1>Статус на плащане</h1>
<div class="card">
    <?php if ($paid): ?>
    <p class="alert alert-success">Плащането е успешно. Услугата е активирана.</p>
    <p>
        <a href="<?= htmlspecialchars($successUrl) ?>" class="btn btn-primary">Продължи</a>
        <?php if (!empty($invoice['id'])): ?>
        <a href="/dashboard/invoices/<?= (int) $invoice['id'] ?>/print" class="btn btn-outline" target="_blank" rel="noopener">Оригинална фактура (PDF)</a>
        <a href="/dashboard/invoices" class="btn btn-outline">Всички фактури</a>
        <?php endif; ?>
    </p>
    <?php elseif (($payment['status'] ?? '') === 'pending'): ?>
    <p class="alert" style="background:#fff3cd">Плащането очаква потвърждение.</p>
    <?php if (($payment['method'] ?? '') === 'bank'): ?>
    <p>Референция: <strong><?= htmlspecialchars(\App\Services\PaymentService::bankReference($payment)) ?></strong></p>
    <p><a href="/payment/bank/<?= htmlspecialchars($payment['public_token']) ?>">Виж банкови инструкции</a></p>
    <?php if (!empty($proforma['id'])): ?>
    <p><a href="/dashboard/invoices/<?= (int) $proforma['id'] ?>/print" class="btn btn-outline" target="_blank" rel="noopener">Проформа фактура (PDF)</a></p>
    <?php endif; ?>
    <?php else: ?>
    <p><a href="/payment/go/<?= htmlspecialchars($payment['public_token']) ?>" class="btn btn-primary">Продължи плащането онлайн</a></p>
    <?php endif; ?>
    <?php elseif (($payment['status'] ?? '') === 'cancelled'): ?>
    <p class="alert" style="background:#fee2e2">Плащането е отменено.</p>
    <?php else: ?>
    <p>Статус: <?= htmlspecialchars($payment['status'] ?? '') ?></p>
    <?php endif; ?>
</div>
