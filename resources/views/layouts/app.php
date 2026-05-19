<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['name']) ?> — Табло</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;width:100%;">
        <a href="/dashboard" class="brand"><?= htmlspecialchars($config['name']) ?></a>
        <nav>
            <span style="opacity:0.9"><?= htmlspecialchars($user['name'] ?? '') ?></span>
            <?php if (\App\Core\Auth::isAdmin()): ?><a href="/admin">Админ</a><?php endif; ?>
            <?php if (\App\Core\Auth::isSuperAdmin()): ?><a href="/super-admin">Супер админ</a><?php endif; ?>
            <form action="/logout" method="post" style="display:inline">
    <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline" style="color:#fff;border-color:#fff">Изход</button></form>
        </nav>
    </div>
</header>
<main class="container sidebar-layout">
    <aside class="sidebar">
        <a href="/dashboard">Табло</a>
        <a href="/dashboard/lofts">Голубарници</a>
        <a href="/dashboard/birds">Птици</a>
        <a href="/dashboard/breeding">Развъждане</a>
        <a href="/dashboard/health">Здраве</a>
        <a href="/dashboard/training">Тренировки</a>
        <a href="/dashboard/competitions">Състезания</a>
        <a href="/dashboard/subscription">Абонамент</a>
        <a href="/dashboard/profile">Профил</a>
    </aside>
    <section>
        <?php if ($msg = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($msg = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?= $content ?>
    </section>
</main>
</body>
</html>
