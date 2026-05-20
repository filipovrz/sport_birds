<h1>Редактирай</h1>
<div class="card">
<form method="post" action="/dashboard/profile">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
    <div class="form-group"><label>Имейл</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
    <div class="form-group"><label>Телефон</label><input name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
    <div class="form-group"><label>Град</label><input name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"></div>
    <div class="form-group"><label>Клуб</label><input name="club_name" value="<?= htmlspecialchars($user['club_name'] ?? '') ?>"></div>
    <h2 style="margin-top:1.5rem;font-size:1.1rem">Данни за фактуриране</h2>
    <p class="text-muted" style="margin-bottom:1rem">По избор — използват се при издаване на фактура след плащане.</p>
    <div class="form-group"><label>Фирма / име за фактура</label><input name="invoice_firm_name" value="<?= htmlspecialchars($user['invoice_firm_name'] ?? '') ?>" placeholder="<?= htmlspecialchars($user['name']) ?>"></div>
    <div class="form-group"><label>ЕИК</label><input name="invoice_eik" value="<?= htmlspecialchars($user['invoice_eik'] ?? '') ?>"></div>
    <div class="form-group"><label>ДДС №</label><input name="invoice_vat_id" value="<?= htmlspecialchars($user['invoice_vat_id'] ?? '') ?>"></div>
    <div class="form-group"><label>Адрес за фактура</label><textarea name="invoice_address" rows="2"><?= htmlspecialchars($user['invoice_address'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Нова парола</label><input type="password" name="password" placeholder="Празно = без промяна"></div>
    <?php require __DIR__ . '/_privacy.php'; ?>
    <p style="margin-top:1rem">
        <button type="submit" class="btn btn-primary">Запази</button>
        <a href="/community/users/<?= (int)$user['id'] ?>" class="btn btn-outline">Виж публичния си профил</a>
    </p>
</form>
</div>
