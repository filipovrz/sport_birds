<?php
use App\Core\Auth;
use App\Services\AdminPermissionService;
?>
<a href="/">← Начало</a>
<?php if (Auth::isSuperAdmin()): ?>
<a href="/super-admin/admins">Администратори</a>
<?php endif; ?>
<a href="/admin">Обзор</a>
<?php if (AdminPermissionService::can('users')): ?>
<a href="/admin/users">Потребители</a>
<?php endif; ?>
<?php if (AdminPermissionService::can('plans')): ?>
<a href="/admin/plans">Планове</a>
<?php endif; ?>
<?php if (AdminPermissionService::can('subscriptions')): ?>
<a href="/admin/subscriptions">Абонаменти</a>
<a href="/admin/invoices">Фактури</a>
<?php endif; ?>
<?php if (AdminPermissionService::can('announcements') || AdminPermissionService::can('events')): ?>
<?php $variant = 'admin'; require __DIR__ . '/_nav_announcements.php'; ?>
<?php endif; ?>
<?php if (AdminPermissionService::can('settings')): ?>
<a href="/admin/settings">Настройки</a>
<a href="/admin/footer">Футър и политики</a>
<?php endif; ?>
