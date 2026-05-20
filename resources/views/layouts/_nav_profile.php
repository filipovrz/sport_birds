<?php
/** @var string $variant sidebar */
$variant = $variant ?? 'sidebar';
if ($variant !== 'sidebar') {
    return;
}
?>
<details class="nav-group">
    <summary>Профил</summary>
    <a href="/dashboard">Статистика</a>
    <a href="/dashboard/subscription">Абонамент</a>
    <a href="/dashboard/gps">GPS устройства</a>
    <a href="/dashboard/profile">Редактирай</a>
    <a href="/dashboard/invoices">Фактури</a>
</details>
