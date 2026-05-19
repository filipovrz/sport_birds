<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ — <?= htmlspecialchars($config['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="app-shell">
<header class="site-header">
    <div class="container site-header__inner">
        <a href="/admin" class="brand">Админ панел</a>
        <nav class="site-header__nav">
            <a href="/">Начало</a>
            <a href="/dashboard">Към табло</a>
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
            <button type="button" class="btn btn-outline sidebar-toggle" data-sidebar-toggle aria-expanded="false" aria-controls="admin-sidebar">☰ Меню</button>
        </div>
        <aside class="sidebar" id="admin-sidebar">
            <?php require __DIR__ . '/_admin_sidebar.php'; ?>
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
