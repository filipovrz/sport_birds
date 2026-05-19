<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap">
    <h1>Обяви за събития</h1>
    <?php if (\App\Core\Auth::check()): ?>
    <div>
        <a href="/dashboard/events/create" class="btn btn-primary btn-sm">+ Публикувай събитие</a>
        <a href="/dashboard/events/my" class="btn btn-outline btn-sm">Моите обяви</a>
    </div>
    <?php endif; ?>
</div>
<p style="color:var(--muted);margin-bottom:1rem">Сборове, събори, срещи и други събития — незадължително състезания.</p>
<?php if (\App\Core\Auth::check() && !\App\Core\Auth::isAdmin() && $publishFee > 0): ?>
<p style="color:var(--muted);margin-bottom:1rem">Публикуване: <strong><?= format_eur($publishFee) ?></strong> (при 0 € в настройките — безплатно)</p>
<?php elseif ($publishFee <= 0): ?>
<p style="color:var(--muted);margin-bottom:1rem">Публикуването на обяви за събития е <strong>безплатно</strong>.</p>
<?php endif; ?>
<div class="grid grid-2">
<?php foreach ($events as $e): ?>
<div class="card">
    <?php if ($e['is_featured']): ?><span class="badge">Препоръчано</span><?php endif; ?>
    <p style="margin:0 0 0.25rem;font-size:0.85rem;color:var(--muted)"><?= event_type_label($e['event_type'] ?? 'gathering') ?></p>
    <h3><a href="/events/<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['title']) ?></a></h3>
    <p><?= date('d.m.Y', strtotime($e['event_date'])) ?><?= !empty($e['event_end_date']) ? ' – ' . date('d.m.Y', strtotime($e['event_end_date'])) : '' ?> · <?= htmlspecialchars($e['location'] ?? '—') ?></p>
    <p style="color:var(--muted);font-size:0.9rem"><?= htmlspecialchars($e['publisher_name']) ?><?= $e['publisher_club'] ? ' · ' . $e['publisher_club'] : '' ?></p>
    <p>Записани: <?= (int)$e['reg_count'] ?><?= $e['max_participants'] ? ' / ' . (int)$e['max_participants'] : '' ?></p>
    <p style="margin-top:0.75rem">
        <?php
        $a = $e;
        $registered = \App\Core\Auth::check() && in_array((int)$e['id'], $myRegIds ?? [], true);
        $isOwner = \App\Core\Auth::check() && (int)$e['user_id'] === \App\Core\Auth::id();
        $canRegister = \App\Core\Auth::check()
            && !$registered
            && !$isOwner
            && (empty($e['registration_deadline']) || $e['registration_deadline'] >= date('Y-m-d'))
            && $e['event_date'] >= date('Y-m-d');
        require __DIR__ . '/_participate.php';
        ?>
    </p>
</div>
<?php endforeach; ?>
</div>
<?php if (empty($events)): ?><p>Няма активни обяви за събития.</p><?php endif; ?>
