<h1><?= htmlspecialchars(__('admin.overview')) ?></h1>
<div class="grid grid-3">
<div class="card stat-card"><div class="num"><?= (int)$stats['users'] ?></div><div><?= htmlspecialchars(__('admin.users')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)$stats['birds'] ?></div><div><?= htmlspecialchars(__('admin.birds')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)$stats['pending_subs'] ?></div><div><?= htmlspecialchars(__('admin.pending_subs')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)($analytics['pending_ann_payments'] ?? 0) ?></div><div><?= htmlspecialchars(__('admin.pending_ann_payments')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)($analytics['pending_event_payments'] ?? 0) ?></div><div><?= htmlspecialchars(__('admin.pending_event_payments')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)($analytics['paid_count_30d'] ?? 0) ?></div><div><?= htmlspecialchars(__('admin.paid_30d')) ?></div></div>
<div class="card stat-card"><div class="num"><?= number_format((float)($analytics['revenue_30d'] ?? 0), 2, ',', ' ') ?></div><div><?= htmlspecialchars(__('admin.revenue_30d')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)($analytics['invoice_count'] ?? 0) ?></div><div><?= htmlspecialchars(__('admin.invoices')) ?></div></div>
<div class="card stat-card"><div class="num"><?= (int)($analytics['proforma_count'] ?? 0) ?></div><div><?= htmlspecialchars(__('admin.proformas')) ?></div></div>
</div>
<div class="card">
    <h3><?= htmlspecialchars(__('admin.health_reminders')) ?></h3>
    <p><?= htmlspecialchars(__('admin.health_reminders_desc')) ?></p>
    <form method="post" action="/admin/health-reminders/send">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary"><?= htmlspecialchars(__('admin.send_reminders')) ?></button>
    </form>
</div>
<p><a href="/admin/invoices" class="btn btn-outline"><?= htmlspecialchars(__('admin.invoices_list')) ?></a></p>
