<?php
/** @var list<array{slug: string, label: string, automatic: bool}> $paymentMethods */
$paymentMethods = $paymentMethods ?? \App\Services\CheckoutFlowService::methodsForForms();
$selected = $selected ?? 'bank';
?>
<div class="form-group payment-methods">
    <label>Начин на плащане *</label>
    <?php foreach ($paymentMethods as $m): ?>
    <label class="payment-method-option" style="display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:0.5rem;cursor:pointer">
        <input type="radio" name="payment_method" value="<?= htmlspecialchars($m['slug']) ?>" <?= $selected === $m['slug'] ? 'checked' : '' ?> required>
        <span><strong><?= htmlspecialchars($m['label']) ?></strong></span>
    </label>
    <?php endforeach; ?>
</div>
