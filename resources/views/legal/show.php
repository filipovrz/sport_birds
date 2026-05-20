<h1><?= htmlspecialchars($title) ?></h1>
<p style="color:var(--muted);font-size:0.9rem;margin-bottom:1rem">Последна актуализация: <?= date('d.m.Y') ?> · Приложимо право: Република България и Регламент (ЕС) 2016/679 (GDPR)</p>
<div class="card legal-content">
    <?= \App\Services\LegalContentService::formatHtml($content) ?>
</div>
