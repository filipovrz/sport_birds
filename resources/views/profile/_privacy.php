<?php /** @var array|null $user */ ?>
<div class="card" style="margin-top:1rem;background:#f8f9fa">
    <h2 style="margin-top:0;font-size:1.05rem">Публична видимост</h2>
    <p style="color:var(--muted);font-size:0.9rem;margin-bottom:0.75rem">Отметнете само ако искате други регистрирани потребители да виждат съответната информация в раздел „Общност“.</p>
    <label style="display:block;margin-bottom:0.5rem">
        <input type="checkbox" name="is_public_profile" <?= !empty($user['is_public_profile']) ? 'checked' : '' ?>>
        Публичен профил (име, град, клуб, специализация)
    </label>
    <label style="display:block;margin-bottom:0.5rem">
        <input type="checkbox" name="default_public_birds" <?= !empty($user['default_public_birds']) ? 'checked' : '' ?>>
        Новите птици по подразбиране да са публични
    </label>
    <label style="display:block;margin-bottom:0.5rem">
        <input type="checkbox" name="default_public_lofts" <?= !empty($user['default_public_lofts']) ? 'checked' : '' ?>>
        Новите гълъбарници по подразбиране да са публични
    </label>
    <label style="display:block">
        <input type="checkbox" name="default_public_breeding" <?= !empty($user['default_public_breeding']) ? 'checked' : '' ?>>
        Новите развъдни двойки по подразбиране да са публични
    </label>
</div>
