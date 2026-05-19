<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Плащане за обява #<?= (int)$ann['id'] ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        @media print { .no-print { display: none; } body { background: #fff; } }
        .doc { max-width: 720px; margin: 0 auto; padding: 2rem; }
        .doc h1 { text-align: center; color: #1e5f8a; font-size: 1.35rem; }
        .doc table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .doc th, .doc td { border: 1px solid #ccc; padding: 0.5rem 0.75rem; text-align: left; vertical-align: top; }
        .doc th { width: 38%; background: #f5f5f5; font-weight: 600; }
    </style>
</head>
<body>
<div class="doc">
    <p class="no-print" style="text-align:center"><button type="button" onclick="window.print()" class="btn btn-primary">Печат / Запази като PDF</button></p>
    <h1><?= htmlspecialchars($config['name'] ?? 'Best Sport Byrds') ?> — Плащане за обява</h1>
    <p style="text-align:center;color:#555">№ <?= (int)$ann['id'] ?> · <?= date('d.m.Y H:i', strtotime($ann['created_at'])) ?></p>

    <table>
        <?php require __DIR__ . '/_payment_fields.php'; ?>
    </table>

    <?php if (!empty($paymentInstructions)): ?>
    <p><strong>Инструкции за плащане (от системата):</strong></p>
    <p style="white-space:pre-wrap"><?= htmlspecialchars($paymentInstructions) ?></p>
    <?php endif; ?>

    <p style="text-align:center;margin-top:2rem;font-size:0.85rem;color:#666">Отпечатано на <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
