<?php
/** @var array<string, mixed> $invoice */
/** @var array<string, mixed> $config */
$totalEur = (float) ($invoice['amount_total_eur'] ?? 0);
$netEur = (float) ($invoice['amount_net_eur'] ?? 0);
$vatEur = (float) ($invoice['amount_vat_eur'] ?? 0);
$totalBgn = isset($invoice['amount_total_bgn']) ? (float) $invoice['amount_total_bgn'] : null;
$vatRate = (float) ($invoice['vat_rate'] ?? 20);
$isProforma = ($invoice['document_type'] ?? 'invoice') === 'proforma';
$docTitle = $isProforma ? 'Проформа фактура / Proforma invoice' : 'Фактура / Invoice';
?>
<div class="card invoice-doc">
    <div class="invoice-doc__header">
        <h2 style="margin:0;color:#1e5f8a"><?= htmlspecialchars($config['name'] ?? 'Best Sport Byrds') ?></h2>
        <p style="margin:0.25rem 0 0;color:#555"><?= htmlspecialchars($docTitle) ?></p>
        <p style="margin:0.5rem 0 0"><strong>№ <?= htmlspecialchars($invoice['invoice_number']) ?></strong>
            · <?= date('d.m.Y', strtotime($invoice['issue_date'])) ?></p>
        <?php if (!$isProforma && !empty($invoice['source_proforma_number'])): ?>
        <p style="margin:0.35rem 0 0;color:#555">По проформа № <?= htmlspecialchars($invoice['source_proforma_number']) ?></p>
        <?php endif; ?>
    </div>

    <div class="invoice-doc__parties" style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:1.5rem">
        <div>
            <h3>Доставчик</h3>
            <?php if (!empty($invoice['seller_firm_name'])): ?>
                <p><strong><?= htmlspecialchars($invoice['seller_firm_name']) ?></strong></p>
            <?php endif; ?>
            <?php if (!empty($invoice['seller_eik'])): ?><p>ЕИК: <?= htmlspecialchars($invoice['seller_eik']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['seller_vat'])): ?><p>ДДС №: <?= htmlspecialchars($invoice['seller_vat']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['seller_address'])): ?><p><?= nl2br(htmlspecialchars($invoice['seller_address'])) ?></p><?php endif; ?>
            <?php if (!empty($invoice['seller_email'])): ?><p><?= htmlspecialchars($invoice['seller_email']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['seller_phone'])): ?><p><?= htmlspecialchars($invoice['seller_phone']) ?></p><?php endif; ?>
            <?php if (empty($invoice['seller_firm_name']) && empty($invoice['seller_eik'])): ?>
                <p class="text-muted">Попълнете фирмените данни в админ → Футър → Информация за фирмата.</p>
            <?php endif; ?>
        </div>
        <div>
            <h3>Получател (поръчител)</h3>
            <p><strong><?= htmlspecialchars($invoice['buyer_name']) ?></strong></p>
            <?php if (!empty($invoice['buyer_eik'])): ?><p>ЕИК: <?= htmlspecialchars($invoice['buyer_eik']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['buyer_vat'])): ?><p>ДДС №: <?= htmlspecialchars($invoice['buyer_vat']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['buyer_address'])): ?><p><?= nl2br(htmlspecialchars($invoice['buyer_address'])) ?></p><?php endif; ?>
            <?php if (!empty($invoice['buyer_email'])): ?><p><?= htmlspecialchars($invoice['buyer_email']) ?></p><?php endif; ?>
            <?php if (!empty($invoice['buyer_phone'])): ?><p><?= htmlspecialchars($invoice['buyer_phone']) ?></p><?php endif; ?>
        </div>
    </div>

    <table class="table" style="margin-top:1.5rem">
        <thead>
            <tr>
                <th>Описание / поръчка</th>
                <th style="text-align:right">Нето</th>
                <th style="text-align:right">ДДС <?= number_format($vatRate, 0) ?>%</th>
                <th style="text-align:right">Общо</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($invoice['line_description']) ?></td>
                <td style="text-align:right"><?= format_eur($netEur) ?></td>
                <td style="text-align:right"><?= format_eur($vatEur) ?></td>
                <td style="text-align:right"><?= format_eur($totalEur) ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right"><?= $isProforma ? 'Сума за плащане' : 'Общо платено' ?></th>
                <th style="text-align:right"><?= format_eur($totalEur) ?></th>
            </tr>
            <?php if ($totalBgn !== null && $totalBgn > 0): ?>
            <tr>
                <td colspan="4" style="text-align:right;color:#555;font-size:0.9rem">
                    Равностойност: <?= number_format($totalBgn, 2, ',', ' ') ?> лв. (фиксиран курс)
                </td>
            </tr>
            <?php endif; ?>
        </tfoot>
    </table>

    <table class="table" style="margin-top:1rem">
        <tr><th style="width:38%">№ поръчка / референция</th><td><strong><?= htmlspecialchars($invoice['payment_reference'] ?? '—') ?></strong></td></tr>
        <tr><th>Начин на плащане</th><td><?= htmlspecialchars($invoice['payment_method'] ?? '—') ?></td></tr>
        <?php if (!$isProforma && !empty($invoice['paid_at'])): ?>
        <tr><th>Платено на</th><td><?= date('d.m.Y H:i', strtotime($invoice['paid_at'])) ?></td></tr>
        <?php elseif ($isProforma): ?>
        <tr><th>Статус</th><td>Очаква банков превод — посочете референцията при превода</td></tr>
        <?php endif; ?>
    </table>

    <p class="text-muted" style="margin-top:1.5rem;font-size:0.85rem">
        <?php if ($isProforma): ?>
        Проформа фактура за банков превод. След постъпване на сумата ще бъде издадена оригинална фактура. Цените са с включен ДДС <?= number_format($vatRate, 0) ?>%.
        <?php else: ?>
        Оригинална фактура, издадена след потвърдено плащане. Цените са с включен ДДС <?= number_format($vatRate, 0) ?>%.
        <?php endif; ?>
    </p>
</div>
