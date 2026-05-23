<?php
/** @var string $variant sidebar */
$variant = $variant ?? 'sidebar';
if ($variant !== 'sidebar') {
    return;
}
?>
<details class="nav-group">
    <summary><?= htmlspecialchars(__('nav.dashboard')) ?></summary>
    <a href="/dashboard/lofts"><?= htmlspecialchars(__('nav.lofts')) ?></a>
    <a href="/dashboard/birds"><?= htmlspecialchars(__('nav.birds')) ?></a>
    <a href="/dashboard/breeding"><?= htmlspecialchars(__('nav.breeding')) ?></a>
    <a href="/dashboard/health"><?= htmlspecialchars(__('nav.health')) ?></a>
    <a href="/dashboard/training"><?= htmlspecialchars(__('nav.training')) ?></a>
</details>
