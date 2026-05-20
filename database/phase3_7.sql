-- v2.1.7: GDPR правна страница
INSERT INTO settings (`key`, `value`) VALUES ('page_gdpr_html', '')
ON DUPLICATE KEY UPDATE `key` = `key`;

UPDATE settings SET `value` = '2.1.7' WHERE `key` = 'app_version';
