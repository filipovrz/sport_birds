<?php /** @var string $action @var string $confirm */ ?>
<form method="post" action="<?= htmlspecialchars($action) ?>" style="display:inline" onsubmit="return confirm(<?= json_encode($confirm, JSON_UNESCAPED_UNICODE) ?>)">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger btn-sm">Изтриване</button>
</form>
