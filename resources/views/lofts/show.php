<h1><?= htmlspecialchars($loft['name']) ?></h1>
<div class="card">
<p><?= htmlspecialchars($loft['location'] ?? '') ?></p>
<a href="/dashboard/lofts/<?= (int)$loft['id'] ?>/edit" class="btn btn-outline btn-sm">Редакция</a>
<h2>Птици в птичарника</h2>
<table><tr><th>Пръстен</th><th>Име</th></tr>
<?php foreach ($birds as $b): ?><tr><td><?= htmlspecialchars($b['ring_number']) ?></td><td><?= htmlspecialchars($b['name'] ?? '—') ?></td></tr><?php endforeach; ?>
</table>
</div>
<form method="post" action="/dashboard/lofts/<?= (int)$loft['id'] ?>/delete" onsubmit="return confirm('Изтриване на птичарника?')">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger btn-sm">Изтрий птичарника</button>
</form>
