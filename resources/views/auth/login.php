<div class="card" style="max-width:420px;margin:2rem auto">
    <h1>Вход</h1>
    <form method="post" action="/login">
        <div class="form-group">
            <label>Имейл</label>
            <input type="email" name="email" required value="<?= htmlspecialchars(\App\Core\Session::flash('old')['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Парола</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Влез</button>
    </form>
    <p style="margin-top:1rem"><a href="/register">Нямате акаунт? Регистрирайте се</a></p>
</div>
