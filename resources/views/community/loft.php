<p><a href="/community">← Общност</a> · <a href="/community/users/<?= (int)$loft['owner_id'] ?>"><?= htmlspecialchars($loft['owner_name']) ?></a></p>
<h1><?= htmlspecialchars($loft['name']) ?></h1>
<div class="card">
    <p><strong>Собственик:</strong> <?= htmlspecialchars($loft['owner_name']) ?><?= $loft['owner_club'] ? ' · ' . htmlspecialchars($loft['owner_club']) : '' ?></p>
    <?php if ($loft['location']): ?><p><strong>Локация:</strong> <?= htmlspecialchars($loft['location']) ?></p><?php endif; ?>
    <?php if ($loft['latitude'] && $loft['longitude']): ?><p><strong>GPS:</strong> <?= htmlspecialchars((string)$loft['latitude']) ?>, <?= htmlspecialchars((string)$loft['longitude']) ?></p><?php endif; ?>
    <?php if ($loft['capacity']): ?><p><strong>Капацитет:</strong> <?= (int)$loft['capacity'] ?></p><?php endif; ?>
    <?php if ($loft['notes']): ?><p><strong>Бележки:</strong><br><?= nl2br(htmlspecialchars($loft['notes'])) ?></p><?php endif; ?>
    <?php if ($isOwner): ?><p><a href="/dashboard/lofts/<?= (int)$loft['id'] ?>" class="btn btn-outline btn-sm">Моят гълъбарник</a></p><?php endif; ?>
</div>
<?php if (!empty($birds)): ?>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Публични птици в гълъбарника</h2>
    <table>
        <tr><th>Пръстен</th><th>Име</th><th></th></tr>
        <?php foreach ($birds as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['ring_number']) ?></td>
            <td><?= htmlspecialchars($b['name'] ?? '—') ?></td>
            <td><a href="/community/birds/<?= (int)$b['id'] ?>">Преглед</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>
