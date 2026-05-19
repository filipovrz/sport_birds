<h1>Настройки</h1>
<p><a href="/admin/footer">Футър, политики и плащания →</a></p>
<div class="card"><form method="post" action="/admin/settings">
    <?= csrf_field() ?>
<div class="form-group"><label>Име на сайта</label><input name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
<div class="form-group"><label>Контакт имейл</label><input name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"></div>
<div class="form-group"><label>Инструкции за плащане</label><textarea name="payment_instructions"><?= htmlspecialchars($settings['payment_instructions'] ?? '') ?></textarea></div>
<hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
<h2 style="margin-top:0">Обяви за състезания</h2>
<div class="form-group">
    <label>Такса за публикуване — състезание (€)</label>
    <input name="announcement_publish_fee_eur" type="number" step="0.01" min="0" value="<?= htmlspecialchars($settings['announcement_publish_fee_eur'] ?? '10.00') ?>">
    <small style="color:var(--muted)">При 0 — безплатно за потребителите.</small>
</div>
<p><a href="/admin/announcement-payments">Плащания състезания →</a> · <a href="/admin/competition-archive">Архив →</a></p>
<hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
<h2 style="margin-top:0">Обяви за събития</h2>
<div class="form-group">
    <label>Такса за публикуване — събитие (€)</label>
    <input name="event_publish_fee_eur" type="number" step="0.01" min="0" value="<?= htmlspecialchars($settings['event_publish_fee_eur'] ?? '5.00') ?>">
    <small style="color:var(--muted)">Сборове, събори, срещи. При 0 — безплатно.</small>
</div>
<p><a href="/admin/event-payments">Плащания събития →</a> · <a href="/admin/event-archive">Архив →</a></p>
<button class="btn btn-primary">Запази</button></form></div>