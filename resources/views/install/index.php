<h1>Инсталация — Best Sport Byrds</h1>
<?php if ($installed): ?>
<p class="alert alert-success">Приложението вече е инсталирано. <a href="/login">Вход</a></p>
<?php else: ?>
<div class="card"><form method="post" action="/install">
<h3>База данни</h3>
<div class="form-group"><label>Хост</label><input name="db_host" value="db"></div>
<div class="form-group"><label>Порт</label><input name="db_port" value="3306"></div>
<div class="form-group"><label>База</label><input name="db_name" value="sport_birds" required></div>
<div class="form-group"><label>Потребител</label><input name="db_user" value="sport_birds" required></div>
<div class="form-group"><label>Парола</label><input name="db_pass" type="password"></div>
<h3>Супер администратор</h3>
<div class="form-group"><label>Имейл</label><input name="admin_email" type="email" required value="filipovrz@gmail.com"></div>
<div class="form-group"><label>Име</label><input name="admin_name" value="Super Admin"></div>
<div class="form-group"><label>Парола (мин. 8)</label><input name="admin_password" type="password" required></div>
<div class="form-group"><label>URL на сайта</label><input name="app_url" value="http://localhost:8080"></div>
<button class="btn btn-primary">Инсталирай</button>
</form></div>
<?php endif; ?>
