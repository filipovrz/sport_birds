<p><a href="/admin/event-payments">← Плащания събития</a></p>
<h1>Плащане #<?= (int)$ev['id'] ?></h1>
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <a href="/admin/event-payments/<?= (int)$ev['id'] ?>/print" class="btn btn-outline" target="_blank">Печат / PDF</a>
    <?php if ($ev['payment_status'] === 'pending'): ?>
    <form method="post" action="/admin/event-payments/<?= (int)$ev['id'] ?>/approve" style="display:inline"><?= csrf_field() ?>
        <label><input type="checkbox" name="is_featured"> Препоръчано</label>
        <button class="btn btn-primary">Одобри и публикувай</button>
    </form>
    <form method="post" action="/admin/event-payments/<?= (int)$ev['id'] ?>/reject" style="display:inline"><?= csrf_field() ?>
        <input name="admin_notes" placeholder="Бележка"><button class="btn btn-danger">Отхвърли</button>
    </form>
    <?php endif; ?>
    <?php $deleteAction = '/admin/event-payments/' . (int)$ev['id'] . '/delete'; $deleteConfirm = 'Изтриване?'; require BASE_PATH . '/resources/views/admin/_delete_form.php'; ?>
</div>
<div class="card"><table style="margin:0"><?php require __DIR__ . '/_payment_fields.php'; ?></table></div>
