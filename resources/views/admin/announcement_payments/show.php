<p><a href="/admin/announcement-payments">← Към плащанията за обяви</a></p>
<h1>Плащане за обява #<?= (int)$ann['id'] ?></h1>

<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <a href="/admin/announcement-payments/<?= (int)$ann['id'] ?>/print" class="btn btn-outline" target="_blank">Печат / PDF</a>
    <?php if (($ann['status'] ?? '') === 'published'): ?>
    <a href="/announcements/<?= (int)$ann['id'] ?>" class="btn btn-outline" target="_blank">Публична обява</a>
    <?php endif; ?>
    <?php if ($ann['payment_status'] === 'pending'): ?>
    <form method="post" action="/admin/announcement-payments/<?= (int)$ann['id'] ?>/approve" style="display:inline">
        <?= csrf_field() ?>
        <label style="font-weight:400;margin-right:0.5rem"><input type="checkbox" name="is_featured"> Препоръчана</label>
        <button type="submit" class="btn btn-primary">Одобри и публикувай</button>
    </form>
    <form method="post" action="/admin/announcement-payments/<?= (int)$ann['id'] ?>/reject" style="display:inline">
        <?= csrf_field() ?>
        <input name="admin_notes" placeholder="Бележка при отхвърляне" style="max-width:200px">
        <button type="submit" class="btn btn-danger">Отхвърли</button>
    </form>
    <?php endif; ?>
    <?php
    $deleteAction = '/admin/announcement-payments/' . (int)$ann['id'] . '/delete';
    $deleteConfirm = 'Сигурни ли сте, че искате да изтриете обявата „' . $ann['title'] . '“?';
    require BASE_PATH . '/resources/views/admin/_delete_form.php';
    ?>
</div>

<div class="card">
    <table style="margin:0">
        <?php require __DIR__ . '/_payment_fields.php'; ?>
    </table>
</div>
