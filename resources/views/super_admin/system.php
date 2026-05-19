<h1>Система</h1>
<div class="card">
<p>PHP: <?= htmlspecialchars($php) ?> | Env: <?= htmlspecialchars($env) ?></p>
<form method="post" action="/super-admin/system">
    <?= csrf_field() ?>
    <label><input type="checkbox" name="maintenance_mode" value="1" <?= !empty($maintenance) ? 'checked' : '' ?>> Режим поддръжка (само админи имат достъп)</label>
    <p style="margin-top:1rem"><button class="btn btn-primary">Запази</button></p>
</form>
</div>
