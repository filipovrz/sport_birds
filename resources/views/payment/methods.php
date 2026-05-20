<h1>Начини на плащане</h1>
<p style="color:var(--muted);max-width:42rem">Плащането се избира при заявка за абонамент или публикуване на обява. Онлайн методите активират услугата автоматично след успешно плащане.</p>

<div class="grid grid-2" style="margin-top:1.5rem">
<?php foreach ($methods as $m):
    $slug = $m['slug'];
    $label = $gatewayLabels[$slug] ?? $m['label'];
?>
    <div class="card">
        <h3 style="margin-top:0"><?= htmlspecialchars($label) ?></h3>
        <?php if ($slug === 'bank'): ?>
        <p style="color:var(--muted);font-size:0.95rem">След превода посочете референцията от екрана за плащане. Активиране след потвърждение от администратор.</p>
        <?php if ($bankInstructions !== ''): ?>
        <div class="payment-methods-bank" style="background:#f8fafc;padding:1rem;border-radius:8px;font-size:0.9rem"><?= nl2br(htmlspecialchars($bankInstructions)) ?></div>
        <?php endif; ?>
        <?php else: ?>
        <p style="color:var(--muted);font-size:0.95rem">Наличен при плащане от таблото — автоматично отчитане.</p>
        <?php endif; ?>
        <?php if (\App\Core\Auth::check()): ?>
        <p style="margin-top:1rem"><a href="/dashboard/subscription" class="btn btn-outline btn-sm">Абонамент</a>
        <a href="/dashboard/announcements/create" class="btn btn-outline btn-sm">Нова обява</a></p>
        <?php else: ?>
        <p style="margin-top:1rem"><a href="/pricing" class="btn btn-outline btn-sm">Цени и планове</a>
        <a href="/login" class="btn btn-outline btn-sm">Вход</a></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
