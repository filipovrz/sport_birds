<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['name']) ?> — Табло</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="app-shell">
<header class="site-header">
    <div class="container site-header__inner">
        <a href="/dashboard" class="brand"><?= htmlspecialchars($config['name']) ?> <small style="font-size:0.65em;opacity:0.85">v<?= htmlspecialchars($config['version'] ?? '1') ?></small></a>
        <nav class="site-header__nav">
            <span class="site-header__user"><?= htmlspecialchars($user['name'] ?? '') ?></span>
            <?php if (\App\Core\Auth::isAdmin()): ?><a href="/admin">Админ</a><?php endif; ?>
            <?php if (\App\Core\Auth::isSuperAdmin()): ?><a href="/super-admin">Супер админ</a><?php endif; ?>
            <form action="/logout" method="post" class="site-header__logout">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline site-header__logout-btn">Изход</button>
            </form>
        </nav>
    </div>
</header>
<main class="container dashboard-main">
    <?php if ($msg = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($msg = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="sidebar-layout">
        <div class="sidebar-mobile-bar">
            <button type="button" class="btn btn-outline sidebar-toggle" data-sidebar-toggle aria-expanded="false" aria-controls="app-sidebar">☰ Меню</button>
        </div>
        <aside class="sidebar" id="app-sidebar">
            <a href="/">← Начало</a>
            <?php $variant = 'sidebar'; require __DIR__ . '/_nav_dashboard.php'; ?>
            <?php require __DIR__ . '/_nav_announcements.php'; ?>
            <?php require __DIR__ . '/_nav_profile.php'; ?>
            <a href="/community">Общност</a>
            <a href="/dashboard/map">Карта</a>
        </aside>
        <section class="dashboard-content">
            <?= $content ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/_footer.php'; ?>
<script src="/assets/js/app-nav.js" defer></script>
</body>
</html>
