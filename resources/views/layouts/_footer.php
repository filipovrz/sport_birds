<?php
use App\Services\FooterService;
use App\Services\SettingsService;

$footer = FooterService::config();
if (empty($footer['enabled'])) {
    return;
}
$siteName = SettingsService::get('site_name') ?: ($config['name'] ?? 'Best Sport Byrds');
$contactEmail = SettingsService::get('contact_email') ?: ($footer['email'] ?? '');
$year = date('Y');
$copyright = trim($footer['copyright'] ?? '');
if ($copyright === '') {
    $copyright = '© ' . $year . ' ' . $siteName;
}
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <strong><?= htmlspecialchars($siteName) ?></strong>
            <?php if (!empty($footer['tagline'])): ?>
            <p><?= htmlspecialchars($footer['tagline']) ?></p>
            <?php endif; ?>
            <?php if (!empty($footer['address'])): ?>
            <p class="site-footer__meta"><?= nl2br(htmlspecialchars($footer['address'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($footer['phone'])): ?>
            <p class="site-footer__meta">Тел.: <?= htmlspecialchars($footer['phone']) ?></p>
            <?php endif; ?>
            <?php if ($contactEmail !== ''): ?>
            <p class="site-footer__meta"><a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a></p>
            <?php endif; ?>
        </div>
        <?php foreach ($footer['columns'] ?? [] as $col): ?>
        <?php if (!empty($col['links'])): ?>
        <div class="site-footer__col">
            <h3><?= htmlspecialchars($col['title'] ?? '') ?></h3>
            <ul>
                <?php foreach ($col['links'] as $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!empty($footer['payment_text'])): ?>
        <div class="site-footer__col">
            <h3><?= htmlspecialchars($footer['payment_title'] ?? 'Начини на плащане') ?></h3>
            <div class="site-footer__payment"><?= nl2br(htmlspecialchars($footer['payment_text'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <div class="container site-footer__bottom">
        <p><?= htmlspecialchars($copyright) ?></p>
    </div>
</footer>
