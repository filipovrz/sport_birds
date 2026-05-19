<div class="card" style="max-width:520px;margin:2rem auto">
    <h1>Потвърдете имейла си</h1>
    <?php if ($msg = \App\Core\Session::flash('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = \App\Core\Session::flash('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <p style="color:var(--muted)">
        Изпратихме линк за потвърждение
        <?php if (!empty($email)): ?>
        на <strong><?= htmlspecialchars($email) ?></strong>
        <?php endif; ?>.
        Отворете имейла и кликнете върху линка, след което можете да влезете.
    </p>
    <?php if ($link = \App\Core\Session::flash('verify_link')): ?>
    <p style="font-size:0.9rem;word-break:break-all"><strong>Линк (само за разработка):</strong><br>
        <a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($link) ?></a>
    </p>
    <?php endif; ?>
    <form method="post" action="/verify-email/resend" style="margin-top:1.25rem">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Имейл</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-outline">Изпрати линка отново</button>
    </form>
    <p style="margin-top:1.25rem"><a href="/login">Към вход</a></p>
</div>
