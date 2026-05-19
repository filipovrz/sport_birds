-- Phase 2.1.3: обяви за събития (сборове, събори и др.)
CREATE TABLE IF NOT EXISTS event_announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    event_type ENUM('gathering','assembly','meeting','exhibition','social','other') NOT NULL DEFAULT 'gathering',
    event_date DATE NOT NULL,
    event_end_date DATE NULL,
    registration_deadline DATE NULL,
    location VARCHAR(255) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    organizer VARCHAR(200) NULL,
    club_name VARCHAR(150) NULL,
    max_participants INT UNSIGNED NULL,
    attendance_fee_eur DECIMAL(10,2) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(30) NULL,
    status ENUM('draft','published','cancelled','completed') NOT NULL DEFAULT 'published',
    payment_status ENUM('not_required','pending','approved','rejected') NOT NULL DEFAULT 'not_required',
    payment_reference VARCHAR(100) NULL,
    publish_fee_eur DECIMAL(10,2) NULL,
    payment_admin_notes TEXT NULL,
    payment_processed_by INT UNSIGNED NULL,
    payment_processed_at DATETIME NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    notes TEXT NULL,
    status ENUM('registered','confirmed','withdrawn') NOT NULL DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_event_user (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES event_announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES ('event_publish_fee_eur', '5.00')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.1.3')
ON DUPLICATE KEY UPDATE `value` = '2.1.3';
