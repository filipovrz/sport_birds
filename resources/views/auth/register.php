<div class="card" style="max-width:560px;margin:2rem auto">
    <h1>Регистрация</h1>
    <form method="post" action="/register">
    <?= csrf_field() ?>
        <div class="form-group"><label>Име</label><input name="name" required></div>
        <div class="form-group"><label>Имейл</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Парола</label><input type="password" name="password" required minlength="8"></div>
        <div class="form-group"><label>Телефон</label><input name="phone"></div>
        <div class="form-group"><label>Град</label><input name="city"></div>
        <div class="form-group"><label>Клуб</label><input name="club_name"></div>
        <div class="form-group">
            <label>Тип потребител</label>
            <label><input type="checkbox" name="user_type[]" value="owner" checked> Собственик</label>
            <label><input type="checkbox" name="user_type[]" value="competitor"> Състезател</label>
            <label><input type="checkbox" name="user_type[]" value="breeder"> Развъдник</label>
        </div>
        <div class="form-group">
            <label>Специализация</label>
            <label><input type="checkbox" name="bird_specialties[]" value="racing_pigeon" checked> Спортни гълъби</label>
            <label><input type="checkbox" name="bird_specialties[]" value="sport_pigeon"> Други гълъби</label>
            <label><input type="checkbox" name="bird_specialties[]" value="other_sport_bird"> Други спортни птици</label>
        </div>
        <?php require __DIR__ . '/../profile/_privacy.php'; ?>
        <div class="form-group" style="margin-top:1rem">
            <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:normal">
                <input type="checkbox" name="accept_terms" value="1" required style="margin-top:0.2rem">
                <span>Прочетох и приемам <a href="/legal/terms" target="_blank" rel="noopener">общите условия</a> и <a href="/legal/privacy" target="_blank" rel="noopener">политиката за поверителност</a>.</span>
            </label>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:normal">
                <input type="checkbox" name="confirm_age" value="1" required style="margin-top:0.2rem">
                <span>Потвърждавам, че съм навършил/навършила съм <strong>16 години</strong>.</span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Регистрирай се</button>
    </form>
</div>
