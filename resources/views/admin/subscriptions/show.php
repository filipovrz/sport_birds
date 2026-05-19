<p><a href="/admin/subscriptions">← Към заявките</a></p>
<h1>Заявка за абонамент #<?= (int)$req['id'] ?></h1>

<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <a href="/admin/subscriptions/<?= (int)$req['id'] ?>/print" class="btn btn-outline" target="_blank">Печат / PDF</a>
    <?php if ($req['status'] === 'pending'): ?>
    <form method="post" action="/admin/subscriptions/<?= (int)$req['id'] ?>/approve" style="display:inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">Одобри</button>
    </form>
    <form method="post" action="/admin/subscriptions/<?= (int)$req['id'] ?>/reject" style="display:inline">
        <?= csrf_field() ?>
        <input name="admin_notes" placeholder="Бележка при отхвърляне" style="max-width:200px">
        <button type="submit" class="btn btn-danger">Отхвърли</button>
    </form>
    <?php endif; ?>
    <?php
    $deleteAction = '/admin/subscriptions/' . (int)$req['id'] . '/delete';
    $deleteConfirm = 'Сигурни ли сте, че искате да изтриете заявката #' . (int)$req['id'] . '?';
    require BASE_PATH . '/resources/views/admin/_delete_form.php';
    ?>
</div>

<div class="card">
    <table style="margin:0">
        <tr><th style="width:220px">Потребител</th><td><?= htmlspecialchars($req['user_name']) ?></td></tr>
        <tr><th>Имейл</th><td><?= htmlspecialchars($req['email']) ?></td></tr>
        <?php if (!empty($req['phone'])): ?>
        <tr><th>Телефон</th><td><?= htmlspecialchars($req['phone']) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['city'])): ?>
        <tr><th>Град</th><td><?= htmlspecialchars($req['city']) ?></td></tr>
        <?php endif; ?>
        <tr><th>План</th><td><?= htmlspecialchars($req['plan_name']) ?></td></tr>
        <tr><th>Сума</th><td><?= format_eur((float)($req['price_eur'] ?? 0)) ?></td></tr>
        <tr><th>Период</th><td><?= format_plan_period($req) ?></td></tr>
        <tr><th>Дата на заявка</th><td><?= date('d.m.Y H:i', strtotime($req['created_at'])) ?></td></tr>
        <tr><th>Референция на плащане</th><td><?= htmlspecialchars($req['payment_reference'] ?? '—') ?></td></tr>
        <tr><th>Статус</th><td><?= subscription_request_status_html($req['status']) ?></td></tr>
        <?php if (!empty($req['notes'])): ?>
        <tr><th>Бележка от потребителя</th><td><?= nl2br(htmlspecialchars($req['notes'])) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['processed_at'])): ?>
        <tr><th>Обработена на</th><td><?= date('d.m.Y H:i', strtotime($req['processed_at'])) ?><?= !empty($req['processed_by_name']) ? ' — ' . htmlspecialchars($req['processed_by_name']) : '' ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($req['admin_notes'])): ?>
        <tr><th>Админ бележка</th><td><?= nl2br(htmlspecialchars($req['admin_notes'])) ?></td></tr>
        <?php endif; ?>
    </table>
</div>
