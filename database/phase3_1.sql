-- Phase 2.1.1: месечни платени планове, безплатен без период
UPDATE subscription_plans SET
    description = 'До 5 птици',
    duration_days = 0
WHERE slug = 'free';

UPDATE subscription_plans SET
    duration_days = 30,
    description = 'До 20 птици'
WHERE slug = 'limited';

UPDATE subscription_plans SET
    duration_days = 30,
    description = 'До 50 птици'
WHERE slug = 'standard';

UPDATE subscription_plans SET
    duration_days = 30,
    description = 'До 100 птици'
WHERE slug = 'popular';

UPDATE subscription_plans SET
    duration_days = 30,
    description = 'Неограничени птици, родословно дърво, експорт'
WHERE slug = 'pro';

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.1.1')
ON DUPLICATE KEY UPDATE `value` = '2.1.1';
