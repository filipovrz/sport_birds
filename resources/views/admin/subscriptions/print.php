<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Заявка за абонамент #<?= (int)$req['id'] ?></title>
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
    <h1><?= htmlspecialchars($config['name'] ?? 'Best Sport Byrds') ?> — Заявка за абонамент</h1>
    <p style="text-align:center;color:#555">№ <?= (int)$req['id'] ?> · <?= date('d.m.Y H:i', strtotime($req['created_at'])) ?></p>

    <table>
        <tr><th>Потребител</th><td><?= htmlspecialchars($req['user_name']) ?></td></tr>
        <tr><th>Имейл</th><td><?= htmlspecialchars($req['email']) ?></td></tr>
        <?php if (!empty($req['phone'])): ?>
        <tr><th>Телефон</th><td><?= htmlspecialchars($req['phone']) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['city'])): ?>
        <tr><th>Град</th><td><?= htmlspecialchars($req['city']) ?></td></tr>
        <?php endif; ?>
        <tr><th>План</th><td><?= htmlspecialchars($req['plan_name']) ?></td></tr>
        <tr><th>Сума</th><td><?= format_eur((float)($req['price_eur'] ?? 0)) ?></td></tr>
        <tr><th>Период</th><td><?= format_plan_period($req) ?></td></tr>
        <tr><th>Референция на плащане</th><td><?= htmlspecialchars($req['payment_reference'] ?? '—') ?></td></tr>
        <tr><th>Статус</th><td><?= subscription_request_status_label($req['status']) ?></td></tr>
        <?php if (!empty($req['notes'])): ?>
        <tr><th>Бележка от потребителя</th><td><?= nl2br(htmlspecialchars($req['notes'])) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['processed_at'])): ?>
        <tr><th>Обработена на</th><td><?= date('d.m.Y H:i', strtotime($req['processed_at'])) ?><?= !empty($req['processed_by_name']) ? ' — ' . htmlspecialchars($req['processed_by_name']) : '' ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['admin_notes'])): ?>
        <tr><th>Админ бележка</th><td><?= nl2br(htmlspecialchars($req['admin_notes'])) ?></td></tr>
        <?php endif; ?>
    </table>

    <?php if (!empty($paymentInstructions)): ?>
    <p><strong>Инструкции за плащане (от системата):</strong></p>
    <p style="white-space:pre-wrap"><?= htmlspecialchars($paymentInstructions) ?></p>
    <?php endif; ?>

    <p style="text-align:center;margin-top:2rem;font-size:0.85rem;color:#666">Отпечатано на <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
