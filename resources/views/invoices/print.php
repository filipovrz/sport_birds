<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Фактура <?= htmlspecialchars($invoice['invoice_number']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        @media print { .no-print { display: none; } body { background: #fff; } }
        .invoice-doc { max-width: 820px; margin: 0 auto; padding: 2rem; }
        .invoice-doc__parties { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem; }
        .invoice-doc__parties h3 { margin: 0 0 0.5rem; font-size: 1rem; color: #1e5f8a; }
        @media (max-width: 640px) { .invoice-doc__parties { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="invoice-doc">
    <p class="no-print" style="text-align:center"><button type="button" onclick="window.print()" class="btn btn-primary">Печат / Запази като PDF</button></p>
    <?php require __DIR__ . '/_body.php'; ?>
</div>
</body>
</html>
