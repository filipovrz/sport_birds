-- Фаза 3.2.0 — проформа при банков превод, оригинална фактура след плащане
ALTER TABLE invoices
    ADD COLUMN document_type ENUM('proforma', 'invoice') NOT NULL DEFAULT 'invoice' AFTER payment_id,
    ADD COLUMN source_proforma_number VARCHAR(32) NULL AFTER invoice_number,
    MODIFY paid_at DATETIME NULL;

ALTER TABLE invoices
    DROP INDEX uk_invoices_payment,
    ADD UNIQUE KEY uk_invoices_payment_type (payment_id, document_type);

UPDATE settings SET `value` = '3.2.0' WHERE `key` = 'app_version';
