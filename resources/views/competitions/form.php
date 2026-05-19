<h1>Ново състезание</h1>
<div class="card"><form method="post" action="/dashboard/competitions">
    <?= csrf_field() ?>
<div class="form-group"><label>Име *</label><input name="name" required></div>
<div class="form-group"><label>Дата *</label><input type="date" name="event_date" required></div>
<div class="form-group"><label>Тип</label>
    <select name="competition_type">
        <?php foreach (competition_type_options() as $t): ?>
        <option value="<?= $t ?>"><?= competition_type_label($t) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group"><label>Дистанция (км)</label><input name="distance_km" type="number" step="0.1"></div>
<div class="form-group"><label>Място</label><input name="location"></div>
<button class="btn btn-primary">Създай</button></form></div>
