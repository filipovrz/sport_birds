<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Състезание — <?= htmlspecialchars($ann['title']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        @media print { .no-print { display: none; } body { background: #fff; } }
        .doc { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .doc h1 { text-align: center; color: #1e5f8a; font-size: 1.35rem; }
        .doc table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .doc th, .doc td { border: 1px solid #ccc; padding: 0.5rem 0.75rem; text-align: left; vertical-align: top; }
        .doc th { background: #f5f5f5; font-weight: 600; }
        .doc .detail th { width: 38%; }
    </style>
</head>
<body>
<div class="doc">
    <p class="no-print" style="text-align:center"><button type="button" onclick="window.print()" class="btn btn-primary">Печат / Запази като PDF</button></p>
    <h1><?= htmlspecialchars($config['name'] ?? 'Best Sport Byrds') ?> — Обява за състезание</h1>
    <p style="text-align:center;color:#555">№ <?= (int)$ann['id'] ?> · <?= date('d.m.Y H:i', strtotime($ann['created_at'])) ?></p>

    <table class="detail">
        <?php require __DIR__ . '/_detail_fields.php'; ?>
    </table>

    <h2 style="font-size:1.1rem;margin-top:1.5rem">Записани участници (<?= count($registrations) ?>)</h2>
    <?php if (empty($registrations)): ?>
    <p>Няма записани участници.</p>
    <?php else: ?>
    <table>
        <tr><th>#</th><th>Дата</th><th>Участник</th><th>Птица</th><th>Бележка</th></tr>
        <?php foreach ($registrations as $i => $r): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= $r['ring_number'] ? htmlspecialchars($r['ring_number']) : '—' ?></td>
            <td><?= $r['notes'] ? htmlspecialchars($r['notes']) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <p style="text-align:center;margin-top:2rem;font-size:0.85rem;color:#666">Отпечатано на <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
