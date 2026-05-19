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
    <div class="form-group"><label>Текст за авторски права (празно = автоматично)</label>
        <input name="footer_copyright" value="<?= htmlspecialchars($footer['copyright'] ?? '') ?>"></div>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
    <h2 style="margin-top:0;font-size:1.1rem">Начини на плащане</h2>
    <div class="form-group"><label>Заглавие</label><input name="footer_payment_title" value="<?= htmlspecialchars($footer['payment_title'] ?? 'Начини на плащане') ?>"></div>
    <div class="form-group"><label>Описание (един ред = един начин)</label>
        <textarea name="footer_payment_text" rows="4"><?= htmlspecialchars($footer['payment_text'] ?? '') ?></textarea></div>
    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border)">
    <h2 style="margin-top:0;font-size:1.1rem">Колони с връзки</h2>
    <p style="color:var(--muted);font-size:0.9rem">На всеки ред: <code>Етикет | /път</code> (напр. <code>Цени | /pricing</code>)</p>
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
    <div class="form-group"><label>Поверителност</label><textarea name="legal_privacy" rows="6"><?= htmlspecialchars($legal['privacy'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Общи условия</label><textarea name="legal_terms" rows="6"><?= htmlspecialchars($legal['terms'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Бисквитки</label><textarea name="legal_cookies" rows="4"><?= htmlspecialchars($legal['cookies'] ?? '') ?></textarea></div>
    <button type="submit" class="btn btn-primary">Запази футъра</button>
</form>
</div>
