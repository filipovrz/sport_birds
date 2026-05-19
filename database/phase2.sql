-- Best Sport Byrds — Phase 2.0.0
-- GPS устройства, проследяване, обяви за състезания

CREATE TABLE IF NOT EXISTS gps_devices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    bird_id INT UNSIGNED NULL,
    serial_number VARCHAR(64) NOT NULL,
    name VARCHAR(150) NOT NULL,
    model VARCHAR(100) NULL,
    api_token VARCHAR(64) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_latitude DECIMAL(10,7) NULL,
    last_longitude DECIMAL(10,7) NULL,
    last_altitude DECIMAL(8,2) NULL,
    last_speed_kmh DECIMAL(8,2) NULL,
    last_battery_pct TINYINT UNSIGNED NULL,
    last_seen_at DATETIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_gps_token (api_token),
    UNIQUE KEY uq_user_serial (user_id, serial_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bird_id) REFERENCES birds(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gps_positions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id INT UNSIGNED NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    altitude DECIMAL(8,2) NULL,
    speed_kmh DECIMAL(8,2) NULL,
    battery_pct TINYINT UNSIGNED NULL,
    recorded_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES gps_devices(id) ON DELETE CASCADE,
    INDEX idx_device_time (device_id, recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS competition_announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    competition_type ENUM('race','show','fight','training_race','other') NOT NULL DEFAULT 'race',
    species ENUM('racing_pigeon','sport_pigeon','gamecock','other') NOT NULL DEFAULT 'racing_pigeon',
    event_date DATE NOT NULL,
    registration_deadline DATE NULL,
    location VARCHAR(255) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    distance_km DECIMAL(10,2) NULL,
    release_latitude DECIMAL(10,7) NULL,
    release_longitude DECIMAL(10,7) NULL,
    organizer VARCHAR(200) NULL,
    club_name VARCHAR(150) NULL,
    max_participants INT UNSIGNED NULL,
    entry_fee_bgn DECIMAL(10,2) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(30) NULL,
    status ENUM('draft','published','cancelled','completed') NOT NULL DEFAULT 'published',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS competition_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    bird_id INT UNSIGNED NULL,
    notes TEXT NULL,
    status ENUM('registered','confirmed','withdrawn') NOT NULL DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ann_user_bird (announcement_id, user_id, bird_id),
    FOREIGN KEY (announcement_id) REFERENCES competition_announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bird_id) REFERENCES birds(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.0.0')
ON DUPLICATE KEY UPDATE `value` = '2.0.0';

UPDATE subscription_plans SET features = '["birds","lofts","breeding","training","health","competitions","gps_tracking","map"]'
WHERE slug = 'standard';

UPDATE subscription_plans SET features = '["all","pedigree_export","public_pedigree","analytics","gps_tracking","map","announcements"]'
WHERE slug = 'pro';
