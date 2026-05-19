-- Phase 2.1.2: такса за публикуване на обяви
ALTER TABLE competition_announcements
    ADD COLUMN payment_status ENUM('not_required', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'not_required' AFTER status,
    ADD COLUMN payment_reference VARCHAR(100) NULL AFTER payment_status,
    ADD COLUMN publish_fee_eur DECIMAL(10,2) NULL AFTER payment_reference,
    ADD COLUMN payment_admin_notes TEXT NULL AFTER publish_fee_eur,
    ADD COLUMN payment_processed_by INT UNSIGNED NULL AFTER payment_admin_notes,
    ADD COLUMN payment_processed_at DATETIME NULL AFTER payment_processed_by;

ALTER TABLE competition_announcements
    ADD CONSTRAINT fk_ann_payment_processor
    FOREIGN KEY (payment_processed_by) REFERENCES users(id) ON DELETE SET NULL;

UPDATE competition_announcements SET payment_status = 'not_required' WHERE status = 'published';

INSERT INTO settings (`key`, `value`) VALUES ('announcement_publish_fee_eur', '10.00')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.1.2')
ON DUPLICATE KEY UPDATE `value` = '2.1.2';
