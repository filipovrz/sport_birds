<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Плащане събитие #<?= (int)$ev['id'] ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>@media print{.no-print{display:none}}body{background:#fff}.doc{max-width:720px;margin:0 auto;padding:2rem}.doc table{width:100%;border-collapse:collapse}.doc th,.doc td{border:1px solid #ccc;padding:0.5rem}.doc th{width:38%;background:#f5f5f5}</style>
</head>
<body>
<div class="doc">
    <p class="no-print" style="text-align:center"><button onclick="window.print()" class="btn btn-primary">Печат / PDF</button></p>
    <h1 style="text-align:center"><?= htmlspecialchars($config['name'] ?? '') ?> — Плащане за събитие</h1>
    <table><?php require __DIR__ . '/_payment_fields.php'; ?></table>
    <?php if (!empty($paymentInstructions)): ?><p><strong>Инструкции:</strong> <?= nl2br(htmlspecialchars($paymentInstructions)) ?></p><?php endif; ?>
    <p style="text-align:center;font-size:0.85rem;color:#666">Отпечатано <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
