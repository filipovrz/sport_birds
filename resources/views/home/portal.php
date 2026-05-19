<section class="hub-hero">
    <div class="hub-badges">
        <span class="hub-badge">v<?= htmlspecialchars($appVersion) ?></span>
        <span class="hub-badge env"><?= htmlspecialchars(strtoupper($env)) ?></span>
        <?php if ($installed): ?>
            <span class="hub-badge">DB v<?= htmlspecialchars($version) ?></span>
        <?php else: ?>
            <span class="hub-badge" style="background:#fde8e8;border-color:#c0392b;color:#721c24">Неинсталирано</span>
        <?php endif; ?>
        <?php if ($isLoggedIn): ?>
            <span class="hub-badge" style="background:#d4edda;border-color:#2d8a5e;color:#155724">Влезли сте</span>
        <?php endif; ?>
    </div>

    <h1>Best Sport Byrds</h1>
    <p class="lead">Карта на всички страници — за разработка и бързо тестване на приложението.</p>

    <div class="hub-quick">
        <?php if (!$installed): ?>
            <a href="/install" class="btn btn-primary">Започни от инсталация</a>
        <?php else: ?>
            <a href="/login" class="btn btn-primary">Вход</a>
            <a href="/register" class="btn btn-accent">Регистрация</a>
            <a href="/dashboard" class="btn btn-outline">Табло</a>
        <?php endif; ?>
    </div>
</section>

<?php if (!$installed): ?>
<div class="hub-alert">
    Приложението още не е инсталирано. Първо отворете <a href="/install"><strong>/install</strong></a>
    (Docker: хост <code>db</code>, потребител <code>sport_birds</code>, парола <code>secret</code>).
</div>
<?php endif; ?>

<nav class="hub-jump" aria-label="Бърз преход">
    <?php foreach ($sections as $sec): ?>
        <a href="#<?= htmlspecialchars($sec['id']) ?>"><?= htmlspecialchars($sec['title']) ?></a>
    <?php endforeach; ?>
</nav>

<?php foreach ($sections as $sec): ?>
<section class="hub-section" id="<?= htmlspecialchars($sec['id']) ?>">
    <div class="hub-section-title">
        <span class="hub-section-icon" aria-hidden="true"><?= $sec['icon'] ?></span>
        <h2><?= htmlspecialchars($sec['title']) ?></h2>
    </div>
    <div class="hub-links">
        <?php foreach ($sec['links'] as $link): ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"
           class="hub-link-card<?= !empty($link['auth']) ? ' auth-required' : '' ?>"
           <?= !empty($link['auth']) && !$isLoggedIn ? 'title="Изисква вход"' : '' ?>>
            <div class="label"><?= htmlspecialchars($link['label']) ?></div>
            <div class="path"><?= htmlspecialchars($link['url']) ?></div>
            <?php if ($link['note']): ?>
                <div class="note"><?= htmlspecialchars($link['note']) ?><?= !empty($link['auth']) && !$isLoggedIn ? ' · вход' : '' ?></div>
            <?php elseif (!empty($link['auth']) && !$isLoggedIn): ?>
                <div class="note">Изисква вход</div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>

<?php if (!empty($plans)): ?>
<section class="hub-section" id="plans">
    <div class="hub-section-title">
        <span class="hub-section-icon">₿</span>
        <h2>Абонаментни планове</h2>
    </div>
    <div class="grid grid-3">
        <?php foreach ($plans as $plan): ?>
        <div class="card" style="margin:0">
            <h3><?= htmlspecialchars($plan['name']) ?></h3>
            <p class="num" style="font-size:1.5rem;font-weight:700;color:var(--primary)"><?= format_plan_price($plan) ?><?= format_plan_price_suffix($plan) ?></p>
            <p style="font-size:0.9rem;color:var(--muted)"><?= format_plan_period($plan) ?><?= ($plan['description'] ?? '') ? ' · ' . htmlspecialchars($plan['description']) : '' ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
