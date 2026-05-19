<h1>Редактирай</h1>
<div class="card">
<form method="post" action="/dashboard/profile">
    <?= csrf_field() ?>
    <div class="form-group"><label>Име</label><input name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
    <div class="form-group"><label>Имейл</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
    <div class="form-group"><label>Телефон</label><input name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
    <div class="form-group"><label>Град</label><input name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"></div>
    <div class="form-group"><label>Клуб</label><input name="club_name" value="<?= htmlspecialchars($user['club_name'] ?? '') ?>"></div>
    <div class="form-group"><label>Нова парола</label><input type="password" name="password" placeholder="Празно = без промяна"></div>
    <?php require __DIR__ . '/_privacy.php'; ?>
    <p style="margin-top:1rem">
        <button type="submit" class="btn btn-primary">Запази</button>
        <a href="/community/users/<?= (int)$user['id'] ?>" class="btn btn-outline">Виж публичния си профил</a>
    </p>
</form>
</div>
