<div class="hero">
    <h1><?= htmlspecialchars($config['name']) ?></h1>
    <p class="tagline"><?= htmlspecialchars($config['tagline']) ?></p>
    <p style="max-width:640px;margin:1rem auto;color:var(--muted)">
        Професионална платформа за собственици и състезатели: спортни гълъби, други спортни птици, бойни петли.
        Регистрация, родословни дървета, развъждане, здраве, тренировки и състезания — на едно място.
    </p>
    <p style="margin-top:1.5rem">
        <a href="/register" class="btn btn-primary">Започнете безплатно</a>
        <a href="/pricing" class="btn btn-outline" style="margin-left:0.5rem">Вижте плановете</a>
    </p>
</div>

<div class="grid grid-3">
    <div class="card">
        <h3>Регистрация на птици</h3>
        <p>Пръстени, породи, статус, връзки родител–потомък и голубарници.</p>
    </div>
    <div class="card">
        <h3>Родословно дърво</h3>
        <p>До 5 поколения, коефициент на инбридинг, публично споделяне (Pro).</p>
    </div>
    <div class="card">
        <h3>Състезания и тренировки</h3>
        <p>Резултати, скорост, точки, дистанции и метеорологични условия.</p>
    </div>
</div>
