-- Phase 2.1.4: публична видимост (профил, птици, гълъбарници, развъждане)
ALTER TABLE users
    ADD COLUMN is_public_profile TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active,
    ADD COLUMN default_public_birds TINYINT(1) NOT NULL DEFAULT 0 AFTER is_public_profile,
    ADD COLUMN default_public_lofts TINYINT(1) NOT NULL DEFAULT 0 AFTER default_public_birds,
    ADD COLUMN default_public_breeding TINYINT(1) NOT NULL DEFAULT 0 AFTER default_public_lofts;

ALTER TABLE birds
    ADD COLUMN is_public TINYINT(1) NOT NULL DEFAULT 0 AFTER is_public_pedigree;

ALTER TABLE lofts
    ADD COLUMN is_public TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;

ALTER TABLE breeding_pairs
    ADD COLUMN is_public TINYINT(1) NOT NULL DEFAULT 0 AFTER notes;

INSERT INTO settings (`key`, `value`) VALUES ('app_version', '2.1.4')
ON DUPLICATE KEY UPDATE `value` = '2.1.4';
