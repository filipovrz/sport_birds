<h1>Нова развъдна двойка</h1>
<div class="card"><form method="post" action="/dashboard/breeding">
    <?= csrf_field() ?>
<div class="form-group"><label>Мъжки</label><select name="male_bird_id" required><?php foreach ($birds as $b): if ($b['sex']==='female') continue; ?><option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['ring_number']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Женски</label><select name="female_bird_id" required><?php foreach ($birds as $b): if ($b['sex']==='male') continue; ?><option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['ring_number']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Сезон</label><input type="number" name="season_year" value="<?= date('Y') ?>"></div>
<div class="form-group"><label>Дата на съединение</label><input type="date" name="paired_at"></div>
<div class="form-group"><label>Дата на яйца</label><input type="date" name="laid_at"></div>
<div class="form-group"><label>Брой яйца</label><input type="number" name="egg_count"></div>
<label style="display:block;margin-top:0.75rem"><input type="checkbox" name="is_public" <?= !empty(\App\Core\Auth::user()['default_public_breeding']) ? 'checked' : '' ?>> <strong>Публична развъдна двойка</strong></label>
<button class="btn btn-primary">Запази</button></form></div>
