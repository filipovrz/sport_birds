<p><a href="/admin/competition-archive">← Към архива</a></p>
<h1><?= htmlspecialchars($ann['title']) ?></h1>

<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <a href="/admin/competition-archive/<?= (int)$ann['id'] ?>/print" class="btn btn-outline" target="_blank">Печат / PDF</a>
    <a href="/admin/competition-archive/<?= (int)$ann['id'] ?>/edit" class="btn btn-primary">Редакция</a>
    <?php if (($ann['status'] ?? '') === 'published'): ?>
    <a href="/announcements/<?= (int)$ann['id'] ?>" class="btn btn-outline" target="_blank">Публична обява</a>
    <?php endif; ?>
    <?php
    $deleteAction = '/admin/competition-archive/' . (int)$ann['id'] . '/delete';
    $deleteConfirm = 'Сигурни ли сте, че искате да изтриете „' . $ann['title'] . '“?';
    require BASE_PATH . '/resources/views/admin/_delete_form.php';
    ?>
</div>

<div class="card">
    <table style="margin:0">
        <?php require __DIR__ . '/_detail_fields.php'; ?>
    </table>
</div>

<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">История на записванията (<?= count($registrations) ?>)</h2>
    <?php if (empty($registrations)): ?>
    <p style="color:var(--muted);margin:0">Няма записани участници.</p>
    <?php else: ?>
    <table>
        <tr><th>Дата</th><th>Участник</th><th>Имейл</th><th>Птица</th><th>Бележка</th><th>Статус</th></tr>
        <?php foreach ($registrations as $r): ?>
        <tr>
            <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= $r['ring_number'] ? htmlspecialchars($r['ring_number']) : '—' ?></td>
            <td><?= $r['notes'] ? htmlspecialchars($r['notes']) : '—' ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
