<h1>Нова тренировка</h1>
<div class="card"><form method="post" action="/dashboard/training">
    <?= csrf_field() ?>
<div class="form-group"><label>Дата</label><input type="date" name="session_date" value="<?= date('Y-m-d') ?>"></div>
<div class="form-group"><label>Гълъбарник</label>
<select name="loft_id"><option value="">—</option>
<?php foreach ($lofts as $l): ?><option value="<?= (int)$l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option><?php endforeach; ?>
</select></div>
<div class="form-group"><label>Дистанция (км)</label><input name="distance_km" type="number" step="0.1"></div>
<div class="form-group"><label>Времетраене (мин)</label><input name="duration_minutes" type="number"></div>
<div class="form-group"><label>Времето</label><input name="weather" placeholder="напр. ясно, вятър 5 m/s"></div>
<div class="form-group"><label>Пуснати / върнали</label>
<input name="birds_released" type="number" placeholder="Пуснати" style="width:48%"> 
<input name="birds_returned" type="number" placeholder="Върнали" style="width:48%"></div>
<div class="form-group"><label>Птици</label>
<?php foreach ($birds as $b): ?><label><input type="checkbox" name="bird_ids[]" value="<?= (int)$b['id'] ?>"> <?= htmlspecialchars($b['ring_number']) ?></label><br><?php endforeach; ?>
</div>
<div class="form-group"><label>Бележки</label><textarea name="notes"></textarea></div>
<button class="btn btn-primary">Запази</button></form></div>
