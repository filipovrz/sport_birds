<h1>Статистика <small style="font-weight:normal;color:var(--muted)">v<?= htmlspecialchars($appVersion ?? '2.0.0') ?></small></h1>

<?php if (!$isPremium): ?><p class="alert" style="background:#fff3cd">Безплатен план — <a href="/dashboard/subscription">надградете</a> за GPS и карта. Обявите се публикуват с отделна такса.</p><?php endif; ?>

<div class="grid grid-3">

    <div class="card stat-card"><div class="num"><?= (int)$stats['birds'] ?></div><div>Птици</div></div>

    <div class="card stat-card"><div class="num"><?= (int)$stats['lofts'] ?></div><div>Гълъбарници</div></div>

    <div class="card stat-card"><div class="num"><?= (int)$stats['gps_active'] ?></div><div>GPS активни</div></div>

    <div class="card stat-card"><div class="num"><?= (int)$stats['announcements_open'] ?></div><div>Отворени обяви</div></div>

    <div class="card stat-card stat-card-plan"><div class="num"><?= htmlspecialchars($plan['name'] ?? '—') ?></div><div>Текущ план</div></div>

</div>

<div class="grid grid-2">

    <div class="card">

        <h3>Предстоящи здравни прегледи</h3>

        <?php if (empty($stats['upcoming_health'])): ?><p>Няма записи.</p>

        <?php else: foreach ($stats['upcoming_health'] as $h): ?>

            <p><?= htmlspecialchars($h['title']) ?> — <?= htmlspecialchars($h['next_due_at']) ?></p>

        <?php endforeach; endif; ?>

    </div>

    <div class="card">

        <h3>Последни състезания (лични)</h3>

        <?php if (empty($stats['recent_competitions'])): ?><p>Няма записи.</p>

        <?php else: foreach ($stats['recent_competitions'] as $c): ?>

            <p><a href="/dashboard/competitions/<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></p>

        <?php endforeach; endif; ?>

    </div>

</div>

<p>

    <a href="/dashboard/birds/create" class="btn btn-primary">+ Нова птица</a>

    <a href="/dashboard/gps/create" class="btn btn-outline">+ GPS</a>

    <a href="/dashboard/map" class="btn btn-outline">Карта</a>

</p>

