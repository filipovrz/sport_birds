<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Best Sport Byrds — управление на спортни птици, родословие, GPS, състезания">
    <title><?= htmlspecialchars($pageTitle ?? $config['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <?php if (!empty($landingPage)): ?>
    <link rel="stylesheet" href="/assets/css/landing.css">
    <?php endif; ?>
</head>
<body class="app-shell<?= !empty($landingPage) ? ' landing-page' : '' ?>">
<header class="site-header">
    <div class="container site-header__inner">
        <a href="/" class="brand"><?= htmlspecialchars($config['name']) ?></a>
        <nav class="site-header__nav">
            <?php $variant = 'header'; require __DIR__ . '/_nav_announcements.php'; ?>
            <a href="/pricing">Цени</a>
            <?php if ($user): ?>
                <a href="/community">Общност</a>
                <a href="/dashboard">Моят акаунт</a>
                <form action="/logout" method="post" style="display:inline">
                    <?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline" style="color:#fff;border-color:#fff">Изход</button>
                </form>
            <?php else: ?>
                <a href="/login">Вход</a>
                <a href="/register" class="btn btn-sm btn-accent">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container" style="max-width:1100px">
    <?php if ($msg = \App\Core\Session::flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($msg = \App\Core\Session::flash('error')): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<?php require __DIR__ . '/_footer.php'; ?>
<?php if (!empty($showDevLink)): ?>
<footer class="site-footer-dev-link"><div class="container"><p><a href="/dev">Карта на страниците (разработка)</a></p></div></footer>
<?php endif; ?>
</body>
</html>
