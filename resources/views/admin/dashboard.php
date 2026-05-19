<h1>Админ обзор</h1>
<div class="grid grid-3">
<div class="card stat-card"><div class="num"><?= (int)$stats['users'] ?></div><div>Потребители</div></div>
<div class="card stat-card"><div class="num"><?= (int)$stats['birds'] ?></div><div>Птици</div></div>
<div class="card stat-card"><div class="num"><?= (int)$stats['pending_subs'] ?></div><div>Чакащи абонаменти</div></div>
</div>
<div class="card">
    <h3>Имейл напомняния</h3>
    <p>Изпраща напомняния за здравни прегледи през следващите 14 дни (изисква работещ <code>mail()</code> на сървъра).</p>
    <form method="post" action="/admin/health-reminders/send">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">Изпрати напомняния</button>
    </form>
</div>
