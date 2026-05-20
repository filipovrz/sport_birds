<?php
use App\Services\FooterService;
use App\Services\PaymentMethodsService;
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
    $copyright = 'Evtinko © ' . $year . ' ' . $siteName;
}
$company = $footer['company'] ?? [];
$companyLines = FooterService::companyLines($company);
$hasCompany = $companyLines !== [];
$paymentMethods = PaymentMethodsService::catalog(false);
$linkColumns = [];
foreach ($footer['columns'] ?? [] as $col) {
    if (!empty($col['links'])) {
        $linkColumns[] = $col;
    }
}
$navCount = ($hasCompany ? 1 : 0) + count($linkColumns) + 1;
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <strong class="site-footer__title"><?= htmlspecialchars($siteName) ?></strong>
            <?php if (!empty($footer['tagline'])): ?>
            <p class="site-footer__tagline"><?= htmlspecialchars($footer['tagline']) ?></p>
            <?php endif; ?>
            <?php if (!empty($footer['address']) || !empty($footer['phone']) || $contactEmail !== ''): ?>
            <ul class="site-footer__contacts">
                <?php if (!empty($footer['address'])): ?>
                <li><?= nl2br(htmlspecialchars($footer['address'])) ?></li>
                <?php endif; ?>
                <?php if (!empty($footer['phone'])): ?>
                <li>Тел.: <?= htmlspecialchars($footer['phone']) ?></li>
                <?php endif; ?>
                <?php if ($contactEmail !== ''): ?>
                <li><a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
        </div>
        <?php if ($navCount > 0): ?>
        <div class="site-footer__nav" style="--footer-nav-cols: <?= max(2, min(4, $navCount)) ?>">
            <?php if ($hasCompany): ?>
            <div class="site-footer__col site-footer__col--company">
                <h3><?= htmlspecialchars($company['title'] ?? 'Информация') ?></h3>
                <ul class="site-footer__company-list">
                    <?php foreach ($companyLines as $line): ?>
                    <li>
                        <span class="site-footer__company-label"><?= htmlspecialchars($line['label']) ?>:</span>
                        <?php if ($line['href']): ?>
                        <a href="<?= htmlspecialchars($line['href']) ?>"><?= htmlspecialchars($line['value']) ?></a>
                        <?php else: ?>
                        <?= nl2br(htmlspecialchars($line['value'])) ?>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php foreach ($linkColumns as $col): ?>
            <div class="site-footer__col">
                <h3><?= htmlspecialchars($col['title'] ?? '') ?></h3>
                <ul>
                    <?php foreach ($col['links'] as $link): ?>
                    <li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
            <div class="site-footer__col site-footer__col--payments">
                <h3>Начини на плащане</h3>
                <ul>
                    <?php foreach ($paymentMethods as $pm): ?>
                    <li><a href="<?= htmlspecialchars(PaymentMethodsService::methodUrl($pm['slug'])) ?>"><?= htmlspecialchars($pm['label']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="/payment-methods">Всички методи →</a></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="container site-footer__bottom">
        <p><?= htmlspecialchars($copyright) ?></p>
    </div>
</footer>
