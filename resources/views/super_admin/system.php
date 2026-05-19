<h1>Система</h1>
<div class="card">
<p>PHP: <?= htmlspecialchars($php) ?> | Env: <?= htmlspecialchars($env) ?></p>
<form method="post" action="/super-admin/system">
<label><input type="checkbox" name="maintenance_mode" value="1"> Режим поддръжка</label>
<button class="btn btn-primary">Запази</button>
</form>
</div>
