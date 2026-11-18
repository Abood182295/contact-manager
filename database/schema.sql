-- Contact Manager - database schema (MySQL 5.7+ / MariaDB 10.3+)
-- Import with:  mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS contact_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contact_manager;

-- -----------------------------------------------------------------------------
-- contacts: id, name, email, phone, created_at
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contacts (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contacts_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- users: the single admin account.
-- Passwords are NEVER stored in plain text: only the output of PHP's
-- password_hash() goes into password_hash. No user is inserted here on purpose;
-- create the admin with:  php scripts/create_admin.php admin
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    username        VARCHAR(50)      NOT NULL,
    password_hash   VARCHAR(255)     NOT NULL,
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME         NULL,
    created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Sample data (fictional people, example.com addresses)
-- -----------------------------------------------------------------------------
INSERT INTO contacts (name, email, phone) VALUES
    ('Sara Alqahtani', 'sara@example.com',  '+966 50 123 4567'),
    ('Omar Hassan',    'omar@example.com',  '+966 55 987 6543'),
    ('Lina Mansour',   'lina@example.com',  '+966 54 111 2222');

-- -----------------------------------------------------------------------------
-- Least-privilege application account (recommended instead of using root).
-- CHANGE THE PASSWORD before running, then put the same one in config/config.php.
-- -----------------------------------------------------------------------------
-- CREATE USER 'cm_user'@'localhost' IDENTIFIED BY 'choose-a-long-random-password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON contact_manager.* TO 'cm_user'@'localhost';
-- FLUSH PRIVILEGES;
