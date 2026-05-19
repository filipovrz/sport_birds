<?php
/** @var string $variant sidebar|header|admin */
$variant = $variant ?? 'sidebar';
$loggedIn = \App\Core\Auth::check();
?>
<?php if ($variant === 'header'): ?>
<div class="nav-dropdown">
    <button type="button" class="nav-dropdown-toggle" aria-expanded="false">Обяви ▾</button>
    <div class="nav-dropdown-menu">
        <a href="/announcements">Състезания</a>
        <a href="/events">Събития</a>
        <?php if ($loggedIn): ?>
        <a href="/dashboard/competitions">Мои състезания</a>
        <?php endif; ?>
    </div>
</div>
<?php elseif ($variant === 'admin'): ?>
<?php
use App\Services\AdminPermissionService;
$canAnn = AdminPermissionService::can('announcements');
$canEv = AdminPermissionService::can('events');
?>
<?php if ($canAnn || $canEv): ?>
<details class="nav-group">
    <summary>Обяви</summary>
    <?php if ($canAnn): ?>
    <a href="/admin/announcement-payments">Плащания — състезания</a>
    <a href="/admin/competition-archive">Архив — състезания</a>
    <?php endif; ?>
    <?php if ($canEv): ?>
    <a href="/admin/event-payments">Плащания — събития</a>
    <a href="/admin/event-archive">Архив — събития</a>
    <?php endif; ?>
</details>
<?php endif; ?>
<?php else: ?>
<details class="nav-group">
    <summary>Обяви</summary>
    <?php if ($loggedIn): ?>
    <a href="/announcements">Състезания</a>
    <a href="/dashboard/announcements/my" class="nav-sub">Моите — състезания</a>
    <a href="/events">Събития</a>
    <a href="/dashboard/events/my" class="nav-sub">Моите — събития</a>
    <a href="/dashboard/competitions" class="nav-sub">Мои състезания</a>
    <?php else: ?>
    <a href="/announcements">Състезания</a>
    <a href="/events">Събития</a>
    <?php endif; ?>
</details>
<?php endif; ?>
