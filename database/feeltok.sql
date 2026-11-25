-- FeelTok database (MariaDB 10.4, XAMPP 8.2.12)
-- Import in phpMyAdmin: Import > choose this file > Import.
-- Test accounts: every password is Test@1234 (prototype only).

CREATE DATABASE IF NOT EXISTS feeltok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE feeltok;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS moods;
DROP TABLE IF EXISTS reactions;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'super_admin') NOT NULL DEFAULT 'user',
    story VARCHAR(300) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    value ENUM('Love', 'Gratitude', 'Apology', 'Appreciation', 'Mindfulness', 'Thank you') NOT NULL,
    text VARCHAR(280) NOT NULL,
    status ENUM('active', 'deleted') NOT NULL DEFAULT 'active',
    delete_reason VARCHAR(200) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    value ENUM('Love', 'Gratitude', 'Apology', 'Appreciation', 'Mindfulness', 'Thank you') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY one_reaction_per_post (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE moods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    mood TINYINT UNSIGNED NOT NULL,
    mood_date DATE NOT NULL,
    UNIQUE KEY one_mood_per_day (user_id, mood_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Test accounts (password: Test@1234)
INSERT INTO users (name, email, password, role) VALUES
('Super Admin', 'superadmin@feeltok.test', '$2y$10$rGcR6kLTxK.B9ucmYWeVu.J/EmyBKgOigpERP.Ar/EjC.IJVTB2sG', 'super_admin'),
('Admin', 'admin@feeltok.test', '$2y$10$9FvkIZvYf18QwtMf.yMO/u.QKyAGUpmLitGi/cHC.fHxEWjYpABD6', 'admin'),
('Sara', 'sara@feeltok.test', '$2y$10$cRBS/VNaUSzRoCzWu6HSz.9wRNESnod3lyGQadrKvAGKe3XvaH20C', 'user'),
('Omar', 'omar@feeltok.test', '$2y$10$2KQXIl9ltbRNBpINaayzbedagdRHZEsxPf/GfR0pt8NQfZFcmqGdS', 'user'),
('Lina', 'lina@feeltok.test', '$2y$10$WPunnPRQvaF839tDmM1eCeOFqOeXAQC9fFJPcEk9Vvo4TPYu4hBSa', 'user'),
('Adam', 'adam@feeltok.test', '$2y$10$HSxrhMNIg6ilj9TTexI8beroRO1MrfPDpq627.EYZ.vR4qprxKnJu', 'user');
