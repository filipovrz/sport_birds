<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['name']) ?> — Портал</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/hub.css">
</head>
<body class="hub-page">
<header class="hub-topbar">
    <div class="container">
        <a href="/" class="hub-brand">Best Sport <span>Byrds</span></a>
        <nav class="hub-topnav">
            <?php if ($installed): ?>
                <a href="/login">Вход</a>
                <a href="/register" class="hub-cta">Регистрация</a>
                <a href="/dashboard">Табло</a>
            <?php else: ?>
                <a href="/install" class="hub-cta">Инсталация</a>
            <?php endif; ?>
            <a href="/pricing">Цени</a>
            <a href="/announcements">Обяви</a>
        </nav>
    </div>
</header>

<main class="container" style="max-width:1100px">
    <?= $content ?>
</main>

<footer class="hub-footer">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['name']) ?> · Версия <?= htmlspecialchars($version ?? '2.0.0') ?></p>
    <p style="margin-top:0.35rem"><a href="https://github.com/filipovrz/sport_birds">GitHub</a></p>
</footer>
</body>
</html>
