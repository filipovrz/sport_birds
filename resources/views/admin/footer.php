<h1>Футър и правни страници</h1>
<p style="color:var(--muted)">Съдържанието се показва на всички публични страници и в таблото.</p>
<div class="card">
<form method="post" action="/admin/footer">
    <?= csrf_field() ?>
    <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem">
        <input type="checkbox" name="footer_enabled" value="1" <?= !empty($footer['enabled']) ? 'checked' : '' ?>>
        <strong>Показвай футъра</strong>
    </label>
    <h2 style="margin-top:0;font-size:1.1rem">Контакти и описание</h2>
    <div class="form-group"><label>Кратко описание</label>
        <textarea name="footer_tagline" rows="2"><?= htmlspecialchars($footer['tagline'] ?? '') ?></textarea></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Адрес</label><textarea name="footer_address" rows="2"><?= htmlspecialchars($footer['address'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Телефон</label><input name="footer_phone" value="<?= htmlspecialchars($footer['phone'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Допълнителен имейл в футъра</label>
        <input type="email" name="footer_email" value="<?= htmlspecialchars($footer['email'] ?? '') ?>">
        <small style="color:var(--muted)">Основният контактен имейл е в „Настройки“.</small>
    </div>
    <div class="form-group"><label>Текст за авторски права</label>
        <input name="footer_copyright" value="<?= htmlspecialchars($footer['copyright'] ?? 'Evtinko © ' . date('Y') . ' Best Sport Byrds') ?>"></div>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
    <h2 style="margin-top:0;font-size:1.1rem">Информация за администратора</h2>
    <?php $co = $footer['company'] ?? []; ?>
    <div class="grid grid-2">
        <div class="form-group"><label>Фирма</label><input name="company_firm_name" value="<?= htmlspecialchars($co['firm_name'] ?? '') ?>"></div>
        <div class="form-group"><label>ЕИК</label><input name="company_eik" value="<?= htmlspecialchars($co['eik'] ?? '') ?>"></div>
        <div class="form-group"><label>ДДС №</label><input name="company_vat" value="<?= htmlspecialchars($co['vat'] ?? '') ?>"></div>
        <div class="form-group"><label>Телефон</label><input name="company_phone" value="<?= htmlspecialchars($co['phone'] ?? '') ?>"></div>
        <div class="form-group"><label>Имейл</label><input type="email" name="company_email" value="<?= htmlspecialchars($co['email'] ?? '') ?>"></div>
        <div class="form-group"><label>Уебсайт</label><input name="company_website" value="<?= htmlspecialchars($co['website'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Адрес</label><textarea name="company_address" rows="2"><?= htmlspecialchars($co['address'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Друго</label><textarea name="company_other" rows="2"><?= htmlspecialchars($co['other'] ?? '') ?></textarea></div>
    <input type="hidden" name="company_title" value="<?= htmlspecialchars($co['title'] ?? 'Информация') ?>">
    <p style="color:var(--muted);font-size:0.9rem;margin:1rem 0">Колона „Начини на плащане“ във футъра се попълва автоматично. IBAN и gateway — <a href="/admin/settings">Настройки</a>.</p>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
    <h2 style="margin-top:0;font-size:1.1rem">Колони с връзки</h2>
    <p style="color:var(--muted);font-size:0.9rem">На всеки ред: <code>Етикет | /път</code> (напр. правни страници). Колона „Информация“ се попълва от секцията по-горе.</p>
    <?php
    $cols = $footer['columns'] ?? [];
    for ($i = 1; $i <= 4; $i++):
        $col = $cols[$i - 1] ?? ['title' => '', 'links' => []];
    ?>
    <div class="card" style="background:#f8fafc;margin-bottom:1rem">
        <div class="form-group"><label>Колона <?= $i ?> — заглавие</label>
            <input name="column_title_<?= $i ?>" value="<?= htmlspecialchars($col['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Връзки</label>
            <textarea name="column_links_<?= $i ?>" rows="4"><?= htmlspecialchars(\App\Services\FooterService::columnsToText($col)) ?></textarea></div>
    </div>
    <?php endfor; ?>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
    <h2 style="margin-top:0;font-size:1.1rem">Правни страници (HTML не е позволен — само текст)</h2>
    <div class="form-group"><label>Поверителност</label><textarea name="legal_privacy" rows="8"><?= htmlspecialchars($legal['privacy'] ?? '') ?></textarea></div>
    <div class="form-group"><label>GDPR</label><textarea name="legal_gdpr" rows="8"><?= htmlspecialchars($legal['gdpr'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Общи условия</label><textarea name="legal_terms" rows="8"><?= htmlspecialchars($legal['terms'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Бисквитки</label><textarea name="legal_cookies" rows="4"><?= htmlspecialchars($legal['cookies'] ?? '') ?></textarea></div>
    <button type="submit" class="btn btn-primary">Запази футъра</button>
</form>
</div>
