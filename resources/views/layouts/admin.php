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
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;width:100%;">
        <a href="/admin" class="brand">Админ панел</a>
        <nav>
            <a href="/">Портал</a>
            <a href="/dashboard">Към табло</a>
            <?php if (\App\Core\Auth::isSuperAdmin()): ?><a href="/super-admin">Супер админ</a><?php endif; ?>
            <form action="/logout" method="post" style="display:inline">
    <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline" style="color:#fff;border-color:#fff">Изход</button></form>
        </nav>
    </div>
</header>
<main class="container sidebar-layout">
    <aside class="sidebar">
        <a href="/">← Портал</a>
        <a href="/admin">Обзор</a>
        <a href="/admin/users">Потребители</a>
        <a href="/admin/plans">Планове</a>
        <a href="/admin/subscriptions">Абонаменти</a>
        <a href="/admin/settings">Настройки</a>
    </aside>
    <section>
        <?php if ($msg = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($msg = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?= $content ?>
    </section>
</main>
</body>
</html>
