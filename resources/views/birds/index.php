<div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Птици</h1>
    <a href="/dashboard/birds/create" class="btn btn-primary">+ Добави</a>
</div>
<div class="card">
<table>
<tr><th>Пръстен</th><th>Име</th><th>Вид</th><th>Гълъбарник</th><th>Статус</th><th></th></tr>
<?php foreach ($birds as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['ring_number']) ?></td>
    <td><?= htmlspecialchars($b['name'] ?? '—') ?></td>
    <td><?= species_label($b['species']) ?></td>
    <td><?= htmlspecialchars($b['loft_name'] ?? '—') ?></td>
    <td><?= status_label($b['status']) ?></td>
    <td>
        <a href="/dashboard/birds/<?= (int)$b['id'] ?>">Преглед</a> |
        <a href="/dashboard/birds/<?= (int)$b['id'] ?>/pedigree">Родословие</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
