<div class="card">
    <h1>Родословно дърво — <?= htmlspecialchars($bird['ring_number']) ?></h1>
    <?php if ($inbreeding !== null): ?>
        <p>Оценка на инбридинг (F): <strong><?= $inbreeding ?></strong></p>
    <?php endif; ?>
    <p>
        <a href="/dashboard/birds/<?= (int)$bird['id'] ?>" class="btn btn-outline btn-sm">← Към птицата</a>
        <?php if ($canExport): ?>
            <a href="/dashboard/birds/<?= (int)$bird['id'] ?>/pedigree/print" class="btn btn-primary btn-sm" target="_blank">Печат / PDF</a>
        <?php endif; ?>
        <?php if ($bird['is_public_pedigree']): ?>
            <a href="/pedigree/public/<?= (int)$bird['id'] ?>" class="btn btn-accent btn-sm" target="_blank">Публичен линк</a>
        <?php endif; ?>
    </p>
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
