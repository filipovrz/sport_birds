<h1>Банков превод</h1>
<div class="card">
    <p><strong>Сума:</strong> <?= format_eur($amountEur) ?>
        <?php if ($amountBgn > 0): ?> (<?= number_format($amountBgn, 2, ',', ' ') ?> лв.)<?php endif; ?>
    </p>
    <p><strong>Основание / референция:</strong> <code style="font-size:1.1rem"><?= htmlspecialchars($reference) ?></code></p>
    <p style="color:var(--muted)">Задължително посочете референцията при превода. След постъпване на сумата администраторът ще активира заявката (обикновено в рамките на 1 работен ден).</p>
    <?php if (!empty($instructions)): ?>
    <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0">
    <h3 style="margin-top:0">Банкови данни</h3>
    <p><?= nl2br(htmlspecialchars($instructions)) ?></p>
    <?php endif; ?>
    <p style="margin-top:1.5rem">
        <a href="/payment/status/<?= htmlspecialchars($payment['public_token']) ?>" class="btn btn-primary">Готово — преглед на статуса</a>
        <a href="/dashboard" class="btn btn-outline">Табло</a>
    </p>
</div>
