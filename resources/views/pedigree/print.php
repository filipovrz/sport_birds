<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Родословие — <?= htmlspecialchars($bird['ring_number']) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        @media print { .no-print { display: none; } body { background: #fff; } }
        .cert { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .cert h1 { text-align: center; color: #1e5f8a; }
    </style>
</head>
<body>
<div class="cert">
    <p class="no-print" style="text-align:center"><button onclick="window.print()" class="btn btn-primary">Печат / Запази като PDF</button></p>
    <h1>Best Sport Byrds — Родословие</h1>
    <p style="text-align:center"><strong><?= htmlspecialchars($bird['ring_number']) ?></strong>
        <?= $bird['name'] ? ' — ' . htmlspecialchars($bird['name']) : '' ?></p>
    <?php if ($inbreeding !== null): ?><p style="text-align:center">Коефициент на инбридинг: <?= $inbreeding ?></p><?php endif; ?>
    <div class="pedigree-tree">
        <?php
        $render = function ($n) use (&$render) {
            if (empty($n)) return;
            echo '<div class="pedigree-node"><strong>' . htmlspecialchars($n['ring_number']) . '</strong>';
            if ($n['name']) echo '<br><small>' . htmlspecialchars($n['name']) . '</small>';
            echo '</div>';
            if (!empty($n['father']) || !empty($n['mother'])) {
                echo '<div class="pedigree-gen">';
                if (!empty($n['father'])) $render($n['father']);
                if (!empty($n['mother'])) $render($n['mother']);
                echo '</div>';
            }
        };
        $render($tree);
        ?>
    </div>
    <p style="text-align:center;margin-top:2rem;font-size:0.85rem;color:#666">Генерирано на <?= date('d.m.Y H:i') ?></p>
</div>
</body>
</html>
