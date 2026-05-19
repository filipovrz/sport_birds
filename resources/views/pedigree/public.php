<div class="hero" style="padding:1.5rem">
    <h1>Публична родословна</h1>
    <p><?= htmlspecialchars($bird['ring_number']) ?> <?= $bird['name'] ? '— '.htmlspecialchars($bird['name']) : '' ?></p>
    <?php if ($owner): ?><p style="color:var(--muted)">Собственик: <?= htmlspecialchars($owner['name']) ?><?= $owner['club_name'] ? ' · '.htmlspecialchars($owner['club_name']) : '' ?></p><?php endif; ?>
</div>
<?php if (!empty($bird['photo_path'])): ?>
<p style="text-align:center"><img src="<?= htmlspecialchars($bird['photo_path']) ?>" style="max-width:240px;border-radius:10px"></p>
<?php endif; ?>
<div class="card pedigree-tree">
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
