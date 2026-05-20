-- Фаза 3.1.0 — фактури след потвърдено плащане
CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    invoice_number VARCHAR(32) NOT NULL,
    issue_date DATE NOT NULL,
    paid_at DATETIME NOT NULL,
    seller_firm_name VARCHAR(255) NULL,
    seller_eik VARCHAR(32) NULL,
    seller_vat VARCHAR(32) NULL,
    seller_address TEXT NULL,
    seller_email VARCHAR(255) NULL,
    seller_phone VARCHAR(64) NULL,
    buyer_name VARCHAR(255) NOT NULL,
    buyer_email VARCHAR(255) NULL,
    buyer_phone VARCHAR(64) NULL,
    buyer_address TEXT NULL,
    buyer_eik VARCHAR(32) NULL,
    buyer_vat VARCHAR(32) NULL,
    line_description VARCHAR(500) NOT NULL,
    payment_method VARCHAR(32) NULL,
    payment_reference VARCHAR(64) NULL,
    amount_total_eur DECIMAL(10,2) NOT NULL,
    amount_total_bgn DECIMAL(10,2) NULL,
    amount_net_eur DECIMAL(10,2) NOT NULL,
    amount_vat_eur DECIMAL(10,2) NOT NULL,
    vat_rate DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_invoices_payment (payment_id),
    UNIQUE KEY uk_invoices_number (invoice_number),
    KEY idx_invoices_user (user_id),
    KEY idx_invoices_issue_date (issue_date),
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users
    ADD COLUMN invoice_firm_name VARCHAR(255) NULL,
    ADD COLUMN invoice_eik VARCHAR(32) NULL,
    ADD COLUMN invoice_vat_id VARCHAR(32) NULL,
    ADD COLUMN invoice_address TEXT NULL;

UPDATE settings SET `value` = '3.1.0' WHERE `key` = 'app_version';
