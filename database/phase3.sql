-- Phase 2.1.0: EUR pricing, subscription plans, гълъбарник terminology support
ALTER TABLE subscription_plans
    CHANGE COLUMN price_bgn price_eur DECIMAL(10,2) NOT NULL DEFAULT 0;

-- Ensure new plan slugs exist; update defaults for annual EUR plans
UPDATE subscription_plans SET
    name = 'Безплатен',
    description = 'До 5 птици — годишен абонамент',
    price_eur = 0,
    duration_days = 365,
    max_birds = 5,
    max_lofts = 1,
    features = '["birds","lofts","basic_health"]',
    sort_order = 0,
    is_active = 1
WHERE slug = 'free';

INSERT INTO subscription_plans (name, slug, description, price_eur, duration_days, max_birds, max_lofts, features, sort_order, is_active)
SELECT 'Ограничен', 'limited', 'До 20 птици — годишен абонамент', 20.00, 365, 20, NULL,
    '["birds","lofts","breeding","health","training"]', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM subscription_plans WHERE slug = 'limited');

UPDATE subscription_plans SET
    name = 'Стандарт',
    description = 'До 50 птици — годишен абонамент',
    price_eur = 40.00,
    duration_days = 365,
    max_birds = 50,
    max_lofts = NULL,
    features = '["birds","lofts","breeding","training","health","competitions","gps_tracking","map"]',
    sort_order = 2,
    is_active = 1
WHERE slug = 'standard';

INSERT INTO subscription_plans (name, slug, description, price_eur, duration_days, max_birds, max_lofts, features, sort_order, is_active)
SELECT 'Най използван', 'popular', 'До 100 птици — годишен абонамент', 70.00, 365, 100, NULL,
    '["birds","lofts","breeding","training","health","competitions","gps_tracking","map","announcements"]', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM subscription_plans WHERE slug = 'popular');

UPDATE subscription_plans SET
    name = 'Професионален',
    description = 'Неограничени птици, родословно дърво, експорт — годишен абонамент',
    price_eur = 100.00,
    duration_days = 365,
    max_birds = NULL,
    max_lofts = NULL,
    features = '["all","pedigree_export","public_pedigree","analytics","gps_tracking","map","announcements"]',
    sort_order = 4,
    is_active = 1
WHERE slug = 'pro';

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.1.0')
ON DUPLICATE KEY UPDATE `value` = '2.1.0';
