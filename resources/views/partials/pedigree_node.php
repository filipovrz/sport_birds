<?php
/** @var array $node */
if (empty($node)) {
    return;
}
?>
<div class="pedigree-node">
    <strong><?= htmlspecialchars($node['ring_number']) ?></strong>
    <?php if ($node['name']): ?><br><small><?= htmlspecialchars($node['name']) ?></small><?php endif; ?>
    <br><small><?= sex_label($node['sex']) ?></small>
</div>
<?php if (!empty($node['father']) || !empty($node['mother'])): ?>
<div class="pedigree-gen">
    <?php if (!empty($node['father'])): ?>
        <?php $node = $node['father']; require __FILE__; ?>
    <?php endif; ?>
    <?php if (!empty($node['mother'])): ?>
        <?php $node = $node['mother']; require __FILE__; ?>
    <?php endif; ?>
</div>
<?php endif; ?>
