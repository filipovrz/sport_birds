<h1><?= htmlspecialchars(__('dashboard.title')) ?> <small style="font-weight:normal;color:var(--muted)">v<?= htmlspecialchars($appVersion ?? '2.0.0') ?></small></h1>

<?php if (!$isPremium): ?><p class="alert" style="background:#fff3cd"><?= htmlspecialchars(__('dashboard.free_plan_hint_1')) ?> <a href="/dashboard/subscription"><?= htmlspecialchars(__('dashboard.upgrade')) ?></a> <?= htmlspecialchars(__('dashboard.free_plan_hint_2')) ?></p><?php endif; ?>

<div class="grid grid-3">
    <div class="card stat-card"><div class="num"><?= (int)$stats['birds'] ?></div><div><?= htmlspecialchars(__('dashboard.birds')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)$stats['lofts'] ?></div><div><?= htmlspecialchars(__('dashboard.lofts')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)$stats['gps_active'] ?></div><div><?= htmlspecialchars(__('dashboard.gps_active')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)$stats['announcements_open'] ?></div><div><?= htmlspecialchars(__('dashboard.announcements_open')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)($analytics['health_overdue'] ?? 0) ?></div><div><?= htmlspecialchars(__('dashboard.health_overdue')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)($analytics['training_30d'] ?? 0) ?></div><div><?= htmlspecialchars(__('dashboard.training_30d')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)($analytics['competitions_ytd'] ?? 0) ?></div><div><?= htmlspecialchars(__('dashboard.competitions_ytd')) ?></div></div>
    <div class="card stat-card"><div class="num"><?= (int)($analytics['breeding_pairs'] ?? 0) ?></div><div><?= htmlspecialchars(__('dashboard.breeding_pairs')) ?></div></div>
    <div class="card stat-card stat-card-plan"><div class="num"><?= htmlspecialchars($plan['name'] ?? '—') ?></div><div><?= htmlspecialchars(__('dashboard.current_plan')) ?></div></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3><?= htmlspecialchars(__('dashboard.health_upcoming')) ?></h3>
        <?php if (empty($stats['upcoming_health'])): ?><p><?= htmlspecialchars(__('dashboard.no_records')) ?></p>
        <?php else: foreach ($stats['upcoming_health'] as $h): ?>
            <p><?= htmlspecialchars($h['title']) ?> — <?= htmlspecialchars($h['next_due_at']) ?></p>
        <?php endforeach; endif; ?>
    </div>
    <div class="card">
        <h3><?= htmlspecialchars(__('dashboard.recent_competitions')) ?></h3>
        <?php if (empty($stats['recent_competitions'])): ?><p><?= htmlspecialchars(__('dashboard.no_records')) ?></p>
        <?php else: foreach ($stats['recent_competitions'] as $c): ?>
            <p><a href="/dashboard/competitions/<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></p>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php if (!empty($analytics['recent_results'])): ?>
<div class="card" style="margin-top:1rem">
    <h3><?= htmlspecialchars(__('dashboard.recent_results')) ?></h3>
    <table class="table">
        <thead><tr><th><?= locale() === 'en' ? 'Competition' : 'Състезание' ?></th><th><?= locale() === 'en' ? 'Bird' : 'Птица' ?></th><th><?= locale() === 'en' ? 'Position' : 'Място' ?></th></tr></thead>
        <tbody>
        <?php foreach ($analytics['recent_results'] as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['competition_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['ring_number'] ?? '—') ?></td>
                <td><?= htmlspecialchars((string)($r['position'] ?? '—')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="card" style="margin-top:1rem">
    <h3><?= htmlspecialchars(__('dashboard.analytics_title')) ?></h3>
    <p class="text-muted"><?= htmlspecialchars(__('dashboard.analytics_desc')) ?></p>
    <?php if ($canExport): ?>
    <p>
        <a href="/dashboard/export/birds.csv" class="btn btn-outline btn-sm"><?= htmlspecialchars(__('dashboard.export_birds')) ?></a>
        <a href="/dashboard/export/lofts.csv" class="btn btn-outline btn-sm"><?= htmlspecialchars(__('dashboard.export_lofts')) ?></a>
        <a href="/dashboard/export/competitions.csv" class="btn btn-outline btn-sm"><?= htmlspecialchars(__('dashboard.export_competitions')) ?></a>
    </p>
    <?php else: ?>
    <p><a href="/dashboard/subscription" class="btn btn-outline btn-sm"><?= htmlspecialchars(__('dashboard.analytics_upgrade')) ?></a></p>
    <?php endif; ?>
</div>

<p style="margin-top:1rem">
    <a href="/dashboard/birds/create" class="btn btn-primary"><?= htmlspecialchars(__('dashboard.new_bird')) ?></a>
    <a href="/dashboard/gps/create" class="btn btn-outline"><?= htmlspecialchars(__('dashboard.new_gps')) ?></a>
    <a href="/dashboard/map" class="btn btn-outline"><?= htmlspecialchars(__('nav.map')) ?></a>
</p>
