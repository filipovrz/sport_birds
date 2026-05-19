<h1>Нова тренировка</h1>
<div class="card"><form method="post" action="/dashboard/training">
<div class="form-group"><label>Дата</label><input type="date" name="session_date" value="<?= date('Y-m-d') ?>"></div>
<div class="form-group"><label>Дистанция (км)</label><input name="distance_km" type="number" step="0.1"></div>
<div class="form-group"><label>Пуснати / върнали</label>
<input name="birds_released" type="number" placeholder="Пуснати" style="width:48%"> 
<input name="birds_returned" type="number" placeholder="Върнали" style="width:48%"></div>
<div class="form-group"><label>Птици</label>
<?php foreach ($birds as $b): ?><label><input type="checkbox" name="bird_ids[]" value="<?= (int)$b['id'] ?>"> <?= htmlspecialchars($b['ring_number']) ?></label><br><?php endforeach; ?>
</div>
<button class="btn btn-primary">Запази</button></form></div>
