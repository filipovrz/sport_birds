-- Премахване на дублиран текст за плащания от настройки (футър вече само линк)
DELETE FROM settings WHERE `key` = 'payment_footer_note';
UPDATE settings SET `value` = '' WHERE `key` = 'payment_methods_json' AND `value` LIKE '%Всички плащания%';

UPDATE settings SET `value` = '3.0.1' WHERE `key` = 'app_version';
