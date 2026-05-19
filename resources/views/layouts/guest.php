<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="app-shell">
<header class="site-header">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;width:100%;">
        <a href="/" class="brand"><?= htmlspecialchars($config['name']) ?></a>
        <nav>
            <a href="/">Портал</a>
            <a href="/pricing">Цени</a>
            <a href="/announcements">Състезания</a>
            <?php if ($user): ?>
                <a href="/dashboard">Табло</a>
                <form action="/logout" method="post" style="display:inline">
    <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline" style="color:#fff;border-color:#fff">Изход</button></form>
            <?php else: ?>
                <a href="/login">Вход</a>
                <a href="/register" class="btn btn-sm btn-accent">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if ($msg = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($msg = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<footer style="text-align:center;padding:2rem;color:var(--muted);font-size:0.9rem">
    &copy; <?= date('Y') ?> <?= htmlspecialchars($config['name']) ?> — Управление на спортни птици
</footer>
</body>
</html>
