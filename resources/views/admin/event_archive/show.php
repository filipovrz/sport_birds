<p><a href="/admin/event-archive">← Архив събития</a></p>
<h1><?= htmlspecialchars($ev['title']) ?></h1>
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <a href="/admin/event-archive/<?= (int)$ev['id'] ?>/print" class="btn btn-outline" target="_blank">Печат / PDF</a>
    <a href="/admin/event-archive/<?= (int)$ev['id'] ?>/edit" class="btn btn-primary">Редакция</a>
    <?php if (($ev['status'] ?? '') === 'published'): ?><a href="/events/<?= (int)$ev['id'] ?>" class="btn btn-outline" target="_blank">Публична обява</a><?php endif; ?>
    <?php $deleteAction = '/admin/event-archive/' . (int)$ev['id'] . '/delete'; $deleteConfirm = 'Изтриване?'; require BASE_PATH . '/resources/views/admin/_delete_form.php'; ?>
</div>
<div class="card"><table style="margin:0"><?php require __DIR__ . '/_detail_fields.php'; ?></table></div>
<div class="card">
    <h2 style="margin-top:0;font-size:1.1rem">Записвания (<?= count($registrations) ?>)</h2>
    <?php if (empty($registrations)): ?><p>Няма записани.</p><?php else: ?>
    <table>
        <tr><th>Дата</th><th>Участник</th><th>Имейл</th><th>Бележка</th></tr>
        <?php foreach ($registrations as $r): ?>
        <tr>
            <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= $r['notes'] ? htmlspecialchars($r['notes']) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
