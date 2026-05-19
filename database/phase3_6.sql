-- v2.1.6: потвърждение на имейл при регистрация
ALTER TABLE users
    ADD COLUMN email_verification_token VARCHAR(64) NULL AFTER email_verified_at,
    ADD COLUMN terms_accepted_at DATETIME NULL AFTER email_verification_token,
    ADD COLUMN age_confirmed_at DATETIME NULL AFTER terms_accepted_at;

UPDATE users SET email_verified_at = COALESCE(email_verified_at, created_at, NOW())
WHERE role IN ('user', 'admin', 'super_admin') AND email_verified_at IS NULL;

UPDATE settings SET `value` = '2.1.6' WHERE `key` = 'app_version';
