<?php
/** @var string $variant sidebar */
$variant = $variant ?? 'sidebar';
if ($variant !== 'sidebar') {
    return;
}
?>
<details class="nav-group">
    <summary><?= htmlspecialchars(__('nav.profile')) ?></summary>
    <a href="/dashboard"><?= htmlspecialchars(__('nav.stats')) ?></a>
    <a href="/dashboard/subscription"><?= htmlspecialchars(__('nav.subscription')) ?></a>
    <a href="/dashboard/gps"><?= htmlspecialchars(__('nav.gps')) ?></a>
    <a href="/dashboard/profile"><?= htmlspecialchars(__('nav.edit_profile')) ?></a>
    <a href="/dashboard/invoices"><?= htmlspecialchars(__('nav.invoices')) ?></a>
</details>
