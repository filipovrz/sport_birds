-- v2.1.5: футър, права на администратори
ALTER TABLE users
    ADD COLUMN admin_permissions JSON NULL COMMENT 'Масив от ключове за права (само role=admin)' AFTER role;

INSERT INTO settings (`key`, `value`) VALUES
('footer_json', ''),
('page_privacy_html', ''),
('page_terms_html', ''),
('page_cookies_html', '')
ON DUPLICATE KEY UPDATE `key` = `key`;

UPDATE settings SET `value` = '2.1.5' WHERE `key` = 'app_version';
