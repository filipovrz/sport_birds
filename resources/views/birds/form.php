<h1><?= $bird ? 'Редакция на птица' : 'Нова птица' ?></h1>
<div class="card">
<form method="post" enctype="multipart/form-data" action="<?= $bird ? '/dashboard/birds/'.$bird['id'] : '/dashboard/birds' ?>">
    <?= csrf_field() ?>
    <div class="grid grid-2">
        <div class="form-group"><label>Пръстен *</label><input name="ring_number" required value="<?= htmlspecialchars($bird['ring_number'] ?? '') ?>"></div>
        <div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($bird['name'] ?? '') ?>"></div>
        <div class="form-group"><label>Вид</label>
            <select name="species">
                <?php
                $speciesOptions = ['racing_pigeon', 'sport_pigeon', 'other'];
                if (!empty($bird['species']) && !in_array($bird['species'], $speciesOptions, true)) {
                    $speciesOptions[] = $bird['species'];
                }
                foreach ($speciesOptions as $s):
                ?>
                <option value="<?= $s ?>" <?= ($bird['species'] ?? '') === $s ? 'selected' : '' ?>><?= species_label($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Пол</label>
            <select name="sex">
                <?php foreach (['male','female','unknown'] as $s): ?>
                <option value="<?= $s ?>" <?= ($bird['sex'] ?? 'unknown') === $s ? 'selected' : '' ?>><?= sex_label($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Гълъбарник</label>
            <select name="loft_id"><option value="">—</option>
                <?php foreach ($lofts as $l): ?>
                <option value="<?= (int)$l['id'] ?>" <?= (int)($bird['loft_id'] ?? 0) === (int)$l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Статус</label>
            <select name="status">
                <?php foreach (['active','breeding','retired','sold','deceased'] as $st): ?>
                <option value="<?= $st ?>" <?= ($bird['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= status_label($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Баща</label>
            <select name="father_id"><option value="">—</option>
                <?php foreach ($parents as $p):
                    $selFather = (int)($bird['father_id'] ?? 0) === (int)$p['id'];
                    if (($p['sex'] ?? '') !== 'male' && !$selFather) {
                        continue;
                    }
                ?>
                <option value="<?= (int)$p['id'] ?>" <?= $selFather ? 'selected' : '' ?>><?= htmlspecialchars($p['ring_number']) ?><?= !empty($p['name']) ? ' — ' . htmlspecialchars($p['name']) : '' ?></option>
                <?php endforeach; ?>
            </select>
            <small style="color:var(--muted)">Само птици с пол „Мъжки“</small>
        </div>
        <div class="form-group"><label>Майка</label>
            <select name="mother_id"><option value="">—</option>
                <?php foreach ($parents as $p):
                    $selMother = (int)($bird['mother_id'] ?? 0) === (int)$p['id'];
                    if (($p['sex'] ?? '') !== 'female' && !$selMother) {
                        continue;
                    }
                ?>
                <option value="<?= (int)$p['id'] ?>" <?= $selMother ? 'selected' : '' ?>><?= htmlspecialchars($p['ring_number']) ?><?= !empty($p['name']) ? ' — ' . htmlspecialchars($p['name']) : '' ?></option>
                <?php endforeach; ?>
            </select>
            <small style="color:var(--muted)">Само птици с пол „Женски“</small>
        </div>
        <div class="form-group"><label>Цвят</label><input name="color" value="<?= htmlspecialchars($bird['color'] ?? '') ?>"></div>
        <div class="form-group"><label>Линия</label><input name="strain" value="<?= htmlspecialchars($bird['strain'] ?? '') ?>"></div>
        <div class="form-group"><label>Роден</label><input type="date" name="birth_date" value="<?= htmlspecialchars($bird['birth_date'] ?? '') ?>"></div>
        <div class="form-group"><label>Снимка</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif">
            <?php if (!empty($bird['photo_path'])): ?>
                <p><img src="<?= htmlspecialchars($bird['photo_path']) ?>" alt="" style="max-width:200px;margin-top:0.5rem;border-radius:8px">
                <br><label><input type="checkbox" name="remove_photo"> Премахни снимката</label></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group"><label>Бележки</label><textarea name="notes"><?= htmlspecialchars($bird['notes'] ?? '') ?></textarea></div>
    <label style="display:block;margin-top:0.75rem"><input type="checkbox" name="is_public" <?= !empty($bird['is_public']) || (!$bird && !empty(\App\Core\Auth::user()['default_public_birds'])) ? 'checked' : '' ?>> <strong>Публична птица</strong> — други потребители виждат пълната информация в „Общност“</label>
    <label style="display:block;margin-top:0.5rem"><input type="checkbox" name="is_public_pedigree" <?= !empty($bird['is_public_pedigree']) ? 'checked' : '' ?>> Публично родословие (анонимен линк без вход)</label>
    <?php if ($bird && !empty($bird['is_public_pedigree'])): ?>
        <p><small>Публичен линк: <a href="/pedigree/public/<?= (int)$bird['id'] ?>" target="_blank">/pedigree/public/<?= (int)$bird['id'] ?></a></small></p>
    <?php endif; ?>
    <p style="margin-top:1rem"><button class="btn btn-primary">Запази</button> <a href="/dashboard/birds">Отказ</a></p>
</form>
</div>
