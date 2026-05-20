<h1>Нова обява за събитие</h1>
<?php if (!empty($requiresPayment)): ?>
<div class="alert" style="background:#eef4f9;border:1px solid var(--primary)">
    <p><strong>Такса за публикуване:</strong> <?= format_eur($publishFee) ?></p>
    <p style="margin:0.5rem 0 0">След изпращане обявата ще бъде публикувана след потвърждение на плащането от администратор.</p>
</div>
<?php elseif (\App\Core\Auth::isAdmin()): ?>
<p style="color:var(--muted)">Като администратор публикувате без такса.</p>
<?php else: ?>
<p style="color:var(--muted)">Публикуването е безплатно (таксата в настройките е 0 €).</p>
<?php endif; ?>
<div class="card">
<form method="post" action="/dashboard/events">
    <?= csrf_field() ?>
    <div class="form-group"><label>Заглавие *</label><input name="title" required></div>
    <div class="form-group"><label>Вид събитие</label>
        <select name="event_type">
            <?php foreach (['gathering','assembly','meeting','exhibition','social','other'] as $t): ?>
            <option value="<?= $t ?>"><?= event_type_label($t) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Описание</label><textarea name="description"></textarea></div>
    <div class="grid grid-2">
        <div class="form-group"><label>Начална дата *</label><input type="date" name="event_date" required></div>
        <div class="form-group"><label>Крайна дата</label><input type="date" name="event_end_date"></div>
        <div class="form-group"><label>Краен срок за запис</label><input type="date" name="registration_deadline"></div>
        <div class="form-group"><label>Място</label><input name="location"></div>
        <div class="form-group"><label>Макс. участници</label><input name="max_participants" type="number" min="0"></div>
    </div>
    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lng">
    <p>Кликнете на картата за локация:</p>
    <div id="ev-map" style="height:260px;border-radius:8px;margin-bottom:1rem"></div>
    <div class="form-group"><label>Клуб / организатор</label><input name="club_name"></div>
    <div class="form-group"><label>Такса за участие в събитието (€)</label><input name="attendance_fee_eur" type="number" step="0.01"><small style="color:var(--muted)"> По желание — за гостите, не за публикуване</small></div>
    <?php if (!empty($requiresPayment)): ?>
    <?php require BASE_PATH . '/resources/views/payment/_methods.php'; ?>
    <?php if (!empty($paymentInstructions)): ?>
    <div class="card" style="background:#f8f9fa;margin-bottom:1rem"><p style="margin:0"><?= nl2br(htmlspecialchars($paymentInstructions)) ?></p></div>
    <?php endif; ?>
    <?php elseif (\App\Core\Auth::isAdmin()): ?>
    <label><input type="checkbox" name="is_featured"> Препоръчано събитие</label>
    <?php endif; ?>
    <p style="margin-top:1rem"><button class="btn btn-primary"><?= !empty($requiresPayment) ? 'Изпрати за одобрение' : 'Публикувай' ?></button></p>
</form>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initBsMap('ev-map', [], { pickable: true, latInput: 'lat', lngInput: 'lng', zoom: 8 });
});
</script>
