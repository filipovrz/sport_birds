<h1><?= $plan ? 'Редакция на план' : 'Нов план' ?></h1>
<div class="card">
<form method="post" action="<?= $plan ? '/admin/plans/'.$plan['id'] : '/admin/plans' ?>">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($plan['name'] ?? '') ?>" required></div>
    <div class="form-group"><label>Slug (код)</label><input name="slug" value="<?= htmlspecialchars($plan['slug'] ?? '') ?>" <?= $plan ? '' : 'required' ?>></div>
    <div class="form-group"><label>Описание</label><textarea name="description" rows="2"><?= htmlspecialchars($plan['description'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Цена (€)</label><input name="price_eur" type="number" step="0.01" min="0" value="<?= plan_price_eur($plan ?? []) ?>"><small style="color:var(--muted)">0 = безплатен (без месечен/годишен период)</small></div>
    <div class="form-group"><label>Продължителност (дни)</label><input name="duration_days" type="number" min="0" value="<?= (int)($plan['duration_days'] ?? 30) ?>"><small style="color:var(--muted)">30 = 1 месец; за безплатен — 0</small></div>
    <div class="form-group"><label>Макс. птици</label><input name="max_birds" type="number" min="0" value="<?= isset($plan['max_birds']) && $plan['max_birds'] !== null ? (int)$plan['max_birds'] : '' ?>" placeholder="празно = неограничено"></div>
    <div class="form-group"><label>Макс. гълъбарници</label><input name="max_lofts" type="number" min="0" value="<?= isset($plan['max_lofts']) && $plan['max_lofts'] !== null ? (int)$plan['max_lofts'] : '' ?>" placeholder="празно = неограничено"></div>
    <div class="form-group"><label>Функции (разделени със запетая)</label>
        <input name="features" value="<?php
            if (!empty($plan['features'])) {
                $f = json_decode($plan['features'], true);
                echo htmlspecialchars(is_array($f) ? implode(', ', $f) : '');
            }
        ?>" placeholder="birds, lofts, pedigree_export, all">
    </div>
    <div class="form-group"><label>Подредба</label><input name="sort_order" type="number" value="<?= (int)($plan['sort_order'] ?? 0) ?>"></div>
    <label><input type="checkbox" name="is_active" <?= ($plan['is_active'] ?? 1) ? 'checked' : '' ?>> Активен</label>
    <p style="margin-top:1rem">
        <button type="submit" class="btn btn-primary">Запази</button>
        <a href="/admin/plans" class="btn btn-outline">Отказ</a>
    </p>
</form>
</div>
