-- Фаза 3.0.0 — онлайн плащания
CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_token VARCHAR(64) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    payable_type VARCHAR(64) NOT NULL,
    payable_id INT UNSIGNED NOT NULL,
    amount_eur DECIMAL(10,2) NOT NULL,
    amount_bgn DECIMAL(10,2) NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    method VARCHAR(32) NOT NULL,
    gateway VARCHAR(32) NULL,
    gateway_session_id VARCHAR(255) NULL,
    gateway_payment_id VARCHAR(255) NULL,
    status ENUM('created','pending','paid','failed','cancelled','refunded') NOT NULL DEFAULT 'created',
    idempotency_key VARCHAR(64) NOT NULL,
    description VARCHAR(255) NULL,
    metadata JSON NULL,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_payments_token (public_token),
    UNIQUE KEY uk_payments_idempotency (idempotency_key),
    KEY idx_payments_payable (payable_type, payable_id),
    KEY idx_payments_user (user_id),
    KEY idx_payments_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE subscription_requests
    ADD COLUMN payment_id INT UNSIGNED NULL,
    ADD COLUMN payment_method VARCHAR(32) NULL;

ALTER TABLE competition_announcements
    ADD COLUMN payment_id INT UNSIGNED NULL;

ALTER TABLE event_announcements
    ADD COLUMN payment_id INT UNSIGNED NULL;

UPDATE settings SET `value` = '3.0.0' WHERE `key` = 'app_version';
