-- Best Sport Byrds — Database Schema
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    price_eur DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration_days INT UNSIGNED NOT NULL DEFAULT 30,
    max_birds INT UNSIGNED NULL,
    max_lofts INT UNSIGNED NULL,
    features JSON NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) DEFAULT 'България',
    role ENUM('user','admin','super_admin') NOT NULL DEFAULT 'user',
    user_type SET('owner','competitor','breeder') NOT NULL DEFAULT 'owner',
    bird_specialties SET('racing_pigeon','sport_pigeon','gamecock','other_sport_bird') NOT NULL DEFAULT 'racing_pigeon',
    club_name VARCHAR(150) NULL,
    federation_id VARCHAR(50) NULL,
    subscription_plan_id INT UNSIGNED NULL,
    subscription_expires_at DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_public_profile TINYINT(1) NOT NULL DEFAULT 0,
    default_public_birds TINYINT(1) NOT NULL DEFAULT 0,
    default_public_lofts TINYINT(1) NOT NULL DEFAULT 0,
    default_public_breeding TINYINT(1) NOT NULL DEFAULT 0,
    email_verified_at DATETIME NULL,
    email_verification_token VARCHAR(64) NULL,
    terms_accepted_at DATETIME NULL,
    age_confirmed_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscription_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    payment_reference VARCHAR(100) NULL,
    notes TEXT NULL,
    admin_notes TEXT NULL,
    processed_by INT UNSIGNED NULL,
    processed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lofts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    capacity INT UNSIGNED NULL,
    notes TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS birds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    loft_id INT UNSIGNED NULL,
    ring_number VARCHAR(50) NOT NULL,
    name VARCHAR(150) NULL,
    species ENUM('racing_pigeon','sport_pigeon','gamecock','other') NOT NULL DEFAULT 'racing_pigeon',
    sex ENUM('male','female','unknown') NOT NULL DEFAULT 'unknown',
    color VARCHAR(100) NULL,
    strain VARCHAR(150) NULL,
    birth_date DATE NULL,
    acquisition_date DATE NULL,
    status ENUM('active','sold','deceased','retired','breeding') NOT NULL DEFAULT 'active',
    father_id INT UNSIGNED NULL,
    mother_id INT UNSIGNED NULL,
    photo_path VARCHAR(255) NULL,
    achievements TEXT NULL,
    notes TEXT NULL,
    is_public_pedigree TINYINT(1) NOT NULL DEFAULT 0,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_ring_user (user_id, ring_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (loft_id) REFERENCES lofts(id) ON DELETE SET NULL,
    FOREIGN KEY (father_id) REFERENCES birds(id) ON DELETE SET NULL,
    FOREIGN KEY (mother_id) REFERENCES birds(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS breeding_pairs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    male_bird_id INT UNSIGNED NOT NULL,
    female_bird_id INT UNSIGNED NOT NULL,
    season_year YEAR NOT NULL,
    paired_at DATE NULL,
    notes TEXT NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (male_bird_id) REFERENCES birds(id) ON DELETE CASCADE,
    FOREIGN KEY (female_bird_id) REFERENCES birds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS breeding_clutches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    breeding_pair_id INT UNSIGNED NOT NULL,
    laid_at DATE NULL,
    hatched_at DATE NULL,
    egg_count TINYINT UNSIGNED NULL,
    hatched_count TINYINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (breeding_pair_id) REFERENCES breeding_pairs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS health_records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    bird_id INT UNSIGNED NULL,
    loft_id INT UNSIGNED NULL,
    record_type ENUM('vaccination','treatment','illness','parasite','checkup','other') NOT NULL,
    title VARCHAR(200) NOT NULL,
    diagnosis TEXT NULL,
    treatment TEXT NULL,
    medication VARCHAR(255) NULL,
    veterinarian VARCHAR(150) NULL,
    cost_bgn DECIMAL(10,2) NULL,
    recorded_at DATE NOT NULL,
    next_due_at DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bird_id) REFERENCES birds(id) ON DELETE CASCADE,
    FOREIGN KEY (loft_id) REFERENCES lofts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    loft_id INT UNSIGNED NULL,
    session_date DATE NOT NULL,
    duration_minutes INT UNSIGNED NULL,
    distance_km DECIMAL(8,2) NULL,
    weather VARCHAR(100) NULL,
    birds_released INT UNSIGNED NULL,
    birds_returned INT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (loft_id) REFERENCES lofts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_birds (
    training_session_id INT UNSIGNED NOT NULL,
    bird_id INT UNSIGNED NOT NULL,
    return_time TIME NULL,
    notes VARCHAR(255) NULL,
    PRIMARY KEY (training_session_id, bird_id),
    FOREIGN KEY (training_session_id) REFERENCES training_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (bird_id) REFERENCES birds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS competitions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    competition_type ENUM('race','show','fight','training_race','other') NOT NULL DEFAULT 'race',
    species ENUM('racing_pigeon','sport_pigeon','gamecock','other') NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(255) NULL,
    distance_km DECIMAL(10,2) NULL,
    release_point VARCHAR(255) NULL,
    organizer VARCHAR(200) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS competition_results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    competition_id INT UNSIGNED NOT NULL,
    bird_id INT UNSIGNED NOT NULL,
    position INT UNSIGNED NULL,
    arrival_time DATETIME NULL,
    velocity_mpm DECIMAL(10,3) NULL,
    points DECIMAL(10,2) NULL,
    prize VARCHAR(150) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (bird_id) REFERENCES birds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Phase 2 tables (included in fresh install)
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

SET FOREIGN_KEY_CHECKS = 1;

-- Default plans
INSERT INTO subscription_plans (name, slug, description, price_eur, duration_days, max_birds, max_lofts, features, sort_order) VALUES
('Безплатен', 'free', 'До 5 птици', 0, 0, 5, 1, '["birds","lofts","basic_health"]', 0),
('Ограничен', 'limited', 'До 20 птици', 20.00, 30, 20, NULL, '["birds","lofts","breeding","health","training"]', 1),
('Стандарт', 'standard', 'До 50 птици', 40.00, 30, 50, NULL, '["birds","lofts","breeding","training","health","competitions","gps_tracking","map"]', 2),
('Най използван', 'popular', 'До 100 птици', 70.00, 30, 100, NULL, '["birds","lofts","breeding","training","health","competitions","gps_tracking","map","announcements"]', 3),
('Професионален', 'pro', 'Неограничени птици, родословно дърво, експорт', 100.00, 30, NULL, NULL, '["all","pedigree_export","public_pedigree","analytics","gps_tracking","map","announcements"]', 4);

INSERT INTO settings (`key`, `value`) VALUES
('app_version', '2.1.4'),
('app_installed', '0'),
('maintenance_mode', '0'),
('site_name', 'Best Sport Byrds'),
('contact_email', 'filipovrz@gmail.com'),
('payment_instructions', 'Банков превод или Revolut — посочете имейла си в основанието.'),
('announcement_publish_fee_eur', '10.00'),
('event_publish_fee_eur', '5.00');
