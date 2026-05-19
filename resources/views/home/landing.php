<section class="landing-hero">
    <h1>Best Sport <em>Byrds</em></h1>
    <p class="tagline"><?= htmlspecialchars($config['tagline']) ?></p>
    <p class="intro">
        Цялостна платформа за собственици, състезатели и развъдници на спортни гълъби,
        други спортни птици. Регистрация, родословия, здраве, GPS проследяване,
        тренировки и състезания — професионално управление от едно място.
    </p>
    <div class="landing-cta">
        <?php if ($installed): ?>
            <a href="/register" class="btn btn-primary">Създайте безплатен акаунт</a>
            <a href="/login" class="btn btn-outline">Вход</a>
        <?php else: ?>
            <a href="/install" class="btn btn-primary">Започнете сега</a>
        <?php endif; ?>
        <a href="/announcements" class="btn btn-outline">Обяви за състезания</a>
    </div>
    <div class="landing-stats">
        <div class="landing-stat">
            <strong>∞</strong>
            <span>Гълъбарници и птици</span>
        </div>
        <div class="landing-stat">
            <strong>GPS</strong>
            <span>Проследяване на птици</span>
        </div>
        <div class="landing-stat">
            <strong>5</strong>
            <span>Поколения родословие</span>
        </div>
    </div>
</section>

<section class="landing-features">
    <h2>Всичко необходимо за отглеждането на Вашите шампиони !</h2>
    <div class="grid grid-3">
        <div class="card landing-feature-card">
            <div class="icon">🕊</div>
            <h3>Регистър на птици</h3>
            <p>Пръстени, линии, статус, снимки и връзки родител–потомък в гълъбарниците ви.</p>
        </div>
        <div class="card landing-feature-card">
            <div class="icon">🌳</div>
            <h3>Родословие</h3>
            <p>Дърво до пет поколения, оценка на инбридинг, печат и публично споделяне.</p>
        </div>
        <div class="card landing-feature-card">
            <div class="icon">📍</div>
            <h3>GPS и карта</h3>
            <p>Регистрирайте вашите GPS устройства и следете позицията на птиците на карта.</p>
        </div>
        <div class="card landing-feature-card">
            <div class="icon">✚</div>
            <h3>Здраве и развъждане</h3>
            <p>Ваксини, лечения, развъдни двойки и напомняния за прегледи.</p>
        </div>
        <div class="card landing-feature-card">
            <div class="icon">🏆</div>
            <h3>Състезания</h3>
            <p>Лични резултати, обяви за гонки и записване на участници.</p>
        </div>
        <div class="card landing-feature-card">
            <div class="icon">📊</div>
            <h3>Планове и растеж</h3>
            <p>Безплатен старт, разширени възможности с абонамент по вашите нужди.</p>
        </div>
    </div>
</section>

<section class="landing-audience">
    <div class="card">
        <h2 style="margin-top:0;text-align:center">За кого е платформата</h2>
        <ul>
            <li>Собственици на спортни гълъби</li>
            <li>Състезатели и клубове</li>
            <li>Развъдници</li>
            <li>Други спортни птици</li>
        </ul>
    </div>
</section>

<?php if (!empty($plans)): ?>
<section class="landing-plans">
    <h2>Изберете своя план</h2>
    <p class="sub">Платените планове са месечни, в евро (€) — започнете безплатно и надградете, когато имате нужда</p>
    <div class="grid grid-3">
        <?php foreach ($plans as $i => $plan): ?>
        <div class="card landing-plan-card<?= $plan['slug'] === 'popular' ? ' featured' : '' ?>">
            <h3><?= htmlspecialchars($plan['name']) ?></h3>
            <p class="price"><?= format_plan_price($plan) ?><?php if ($suffix = format_plan_price_suffix($plan)): ?><small style="font-size:0.45em;display:block;color:var(--muted);font-weight:normal"><?= htmlspecialchars($suffix) ?></small><?php endif; ?></p>
            <p style="color:var(--muted);font-size:0.9rem;min-height:3rem"><?= htmlspecialchars($plan['description'] ?? '') ?></p>
            <a href="/register" class="btn <?= $plan['slug'] === 'free' ? 'btn-outline' : 'btn-primary' ?> btn-sm">Избери</a>
        </div>
        <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:1.5rem"><a href="/pricing">Вижте подробно сравнение на плановете →</a></p>
</section>
<?php endif; ?>

<section class="landing-footer-cta">
    <h2>Готови ли сте да организирате отгледа си професионално?</h2>
    <p>Присъединете се към Best Sport Byrds — вашият дигитален гълъбарник винаги под ръка.</p>
    <?php if ($installed): ?>
        <a href="/register" class="btn btn-primary">Регистрация</a>
    <?php else: ?>
        <a href="/install" class="btn btn-primary">Започнете сега</a>
    <?php endif; ?>
    <a href="/pricing" class="btn btn-outline">Цени</a>
</section>
