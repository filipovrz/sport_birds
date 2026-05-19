<?php
/** @var array $a @var bool $registered @var bool $canRegister */
$eventId = (int)$a['id'];
$isLoggedIn = \App\Core\Auth::check();
?>
<?php if ($registered): ?>
<span class="badge" style="background:#d4edda;color:#155724">Записани сте</span>
<?php elseif ($canRegister): ?>
<form method="post" action="/events/<?= $eventId ?>/register" style="display:inline">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-primary btn-sm">Ще участвам</button>
</form>
<?php elseif ($isLoggedIn): ?>
<?php else: ?>
<a href="/login" class="btn btn-primary btn-sm">Ще участвам</a>
<?php endif; ?>
