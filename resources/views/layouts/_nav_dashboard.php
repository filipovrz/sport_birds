<?php
/** @var string $variant sidebar */
$variant = $variant ?? 'sidebar';
if ($variant !== 'sidebar') {
    return;
}
?>
<details class="nav-group">
    <summary>Табло</summary>
    <a href="/dashboard/lofts">Гълъбарници</a>
    <a href="/dashboard/birds">Птици</a>
    <a href="/dashboard/breeding">Развъждане</a>
    <a href="/dashboard/health">Здраве</a>
    <a href="/dashboard/training">Тренировки</a>
</details>
