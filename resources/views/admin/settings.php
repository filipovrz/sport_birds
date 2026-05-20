<h1>Настройки</h1>
<p><a href="/admin/footer">Футър, политики и плащания →</a></p>
<div class="card"><form method="post" action="/admin/settings">
    <?= csrf_field() ?>
<div class="form-group"><label>Име на сайта</label><input name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
<div class="form-group"><label>Контакт имейл</label><input name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>"></div>
<div class="form-group"><label>Банкови реквизити (IBAN, банка, основание)</label>
    <textarea name="payment_instructions" rows="5" placeholder="IBAN: BG00 ...&#10;Основание: имейл + номер на заявка"><?= htmlspecialchars($settings['payment_instructions'] ?? '') ?></textarea>
    <small style="color:var(--muted)">Начините на плащане (карта, ePay.bg и др.) се редактират във <a href="/admin/footer">Футър</a>. Тук са само данни за банков превод.</small>
</div>
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
<hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
<h2 style="margin-top:0">Онлайн плащания (3.0)</h2>
<?php $appUrl = rtrim($config['url'] ?? '', '/'); ?>
<p style="color:var(--muted);font-size:0.9rem">Webhooks: <code><?= htmlspecialchars($appUrl) ?>/webhooks/stripe</code>, <code>/webhooks/epay</code>, <code>/webhooks/paypal</code>, <code>/webhooks/revolut</code></p>
<div class="form-group"><label>EUR → BGN</label><input name="payment_eur_bgn_rate" value="<?= htmlspecialchars($settings['payment_eur_bgn_rate'] ?? '1.95583') ?>"></div>
<div class="grid grid-2">
    <div class="form-group"><label>Stripe secret key</label><input name="stripe_secret_key" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>"></div>
    <div class="form-group"><label>Stripe webhook secret</label><input name="stripe_webhook_secret" value="<?= htmlspecialchars($settings['stripe_webhook_secret'] ?? '') ?>"></div>
    <div class="form-group"><label>ePay MIN</label><input name="epay_min" value="<?= htmlspecialchars($settings['epay_min'] ?? '') ?>"></div>
    <div class="form-group"><label>ePay secret</label><input name="epay_secret" type="password" value="<?= htmlspecialchars($settings['epay_secret'] ?? '') ?>"></div>
    <div class="form-group"><label>ePay URL</label><input name="epay_url" value="<?= htmlspecialchars($settings['epay_url'] ?? 'https://www.epay.bg/') ?>"></div>
    <div class="form-group"><label>PayPal client ID</label><input name="paypal_client_id" value="<?= htmlspecialchars($settings['paypal_client_id'] ?? '') ?>"></div>
    <div class="form-group"><label>PayPal secret</label><input name="paypal_secret" type="password" value="<?= htmlspecialchars($settings['paypal_secret'] ?? '') ?>"></div>
    <div class="form-group"><label>PayPal mode</label><input name="paypal_mode" value="<?= htmlspecialchars($settings['paypal_mode'] ?? 'sandbox') ?>"></div>
    <div class="form-group"><label>Revolut API secret</label><input name="revolut_api_secret" type="password" value="<?= htmlspecialchars($settings['revolut_api_secret'] ?? '') ?>"></div>
    <div class="form-group"><label>Revolut mode</label><input name="revolut_mode" value="<?= htmlspecialchars($settings['revolut_mode'] ?? 'sandbox') ?>"></div>
</div>
<button class="btn btn-primary">Запази</button></form></div>