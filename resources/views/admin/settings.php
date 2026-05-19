<h1>Настройки</h1>
<div class="card"><form method="post" action="/admin/settings">
    <?= csrf_field() ?>
<div class="form-group"><label>Име на сайта</label><input name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
<div class="form-group"><label>Контакт имейл</label><input name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"></div>
<div class="form-group"><label>Инструкции за плащане</label><textarea name="payment_instructions"><?= htmlspecialchars($settings['payment_instructions'] ?? '') ?></textarea></div>
<button class="btn btn-primary">Запази</button></form></div>
