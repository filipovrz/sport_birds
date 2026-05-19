<div class="card">
    <h1>Родословно дърво — <?= htmlspecialchars($bird['ring_number']) ?></h1>
    <?php if ($inbreeding !== null): ?>
        <p>Оценка на инбридинг (F): <strong><?= $inbreeding ?></strong></p>
    <?php endif; ?>
    <a href="/dashboard/birds/<?= (int)$bird['id'] ?>" class="btn btn-outline btn-sm">← Към птицата</a>
</div>
<div class="card pedigree-tree">
    <?php
    $render = function ($n) use (&$render) {
        if (empty($n)) return;
        echo '<div class="pedigree-node"><strong>' . htmlspecialchars($n['ring_number']) . '</strong>';
        if ($n['name']) echo '<br><small>' . htmlspecialchars($n['name']) . '</small>';
        echo '<br><small>' . sex_label($n['sex']) . '</small></div>';
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
