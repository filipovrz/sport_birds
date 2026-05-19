<p><a href="/community">← Общност</a></p>
<h1><?= htmlspecialchars($profile['name']) ?></h1>
<?php if ($isSelf): ?><p><a href="/dashboard/profile" class="btn btn-outline btn-sm">Редактирай профила си</a></p><?php endif; ?>

<div class="card">
    <table style="margin:0">
        <tr><th style="width:200px">Град</th><td><?= htmlspecialchars($profile['city'] ?? '—') ?></td></tr>
        <tr><th>Държава</th><td><?= htmlspecialchars($profile['country'] ?? 'България') ?></td></tr>
        <tr><th>Клуб</th><td><?= htmlspecialchars($profile['club_name'] ?? '—') ?></td></tr>
        <?php if (!empty($profile['federation_id'])): ?>
        <tr><th>Федерация ID</th><td><?= htmlspecialchars($profile['federation_id']) ?></td></tr>
        <?php endif; ?>
        <tr><th>Тип</th><td><?= user_type_labels($profile['user_type'] ?? '') ?></td></tr>
        <tr><th>Специализация</th><td><?= bird_specialty_labels($profile['bird_specialties'] ?? '') ?></td></tr>
        <tr><th>Член от</th><td><?= date('d.m.Y', strtotime($profile['created_at'])) ?></td></tr>
    </table>
</div>

<?php if (!empty($lofts)): ?>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Публични гълъбарници</h2>
    <ul>
    <?php foreach ($lofts as $l): ?>
        <li><a href="/community/lofts/<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></a><?= $l['location'] ? ' — ' . htmlspecialchars($l['location']) : '' ?></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($birds)): ?>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Публични птици</h2>
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

<?php if (!empty($breeding)): ?>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Публично развъждане</h2>
    <ul>
    <?php foreach ($breeding as $bp): ?>
        <li><a href="/community/breeding/<?= (int)$bp['id'] ?>">Сезон <?= (int)$bp['season_year'] ?>: <?= htmlspecialchars($bp['male_ring']) ?> × <?= htmlspecialchars($bp['female_ring']) ?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
