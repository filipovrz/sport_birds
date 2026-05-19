<h1>Профил</h1>
<div class="card"><form method="post" action="/dashboard/profile">
<div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($user['name']) ?>"></div>
<div class="form-group"><label>Имейл</label><input name="email" value="<?= htmlspecialchars($user['email']) ?>"></div>
<div class="form-group"><label>Телефон</label><input name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
<div class="form-group"><label>Нова парола</label><input type="password" name="password" placeholder="Празно = без промяна"></div>
<button class="btn btn-primary">Запази</button></form></div>
