<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ev['title']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>@media print{.no-print{display:none}}.doc{max-width:800px;margin:0 auto;padding:2rem}.doc table{width:100%;border-collapse:collapse}.doc th,.doc td{border:1px solid #ccc;padding:0.5rem}.doc th{width:38%;background:#f5f5f5}</style>
</head>
<body>
<div class="doc">
    <p class="no-print" style="text-align:center"><button onclick="window.print()" class="btn btn-primary">Печат / PDF</button></p>
    <h1 style="text-align:center"><?= htmlspecialchars($config['name'] ?? '') ?> — Събитие</h1>
    <table class="detail"><?php require __DIR__ . '/_detail_fields.php'; ?></table>
    <h2>Участници (<?= count($registrations) ?>)</h2>
    <?php if (!empty($registrations)): ?>
    <table><tr><th>#</th><th>Име</th><th>Дата</th></tr>
    <?php foreach ($registrations as $i => $r): ?>
    <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($r['user_name']) ?></td><td><?= date('d.m.Y', strtotime($r['created_at'])) ?></td></tr>
    <?php endforeach; ?></table>
    <?php endif; ?>
    <p style="text-align:center;font-size:0.85rem;color:#666">Отпечатано <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
