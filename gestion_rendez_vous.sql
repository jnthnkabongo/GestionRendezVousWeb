-- ============================================================
-- Base de données : gestion_rendez_vous
-- Application : Flutter + Laravel + Firebase FCM
-- Compatible : MySQL 8.x / MariaDB récent
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestion_rendez_vous
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_rendez_vous;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS rappels;
DROP TABLE IF EXISTS user_devices;
DROP TABLE IF EXISTS rendez_vous;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. USERS
-- Chefs et secrétaires utilisant l'application
-- ============================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(30) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('chef', 'secretaire') NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. CLIENTS
-- Les personnes ayant un rendez-vous
-- ============================================================

CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NULL,
    telephone VARCHAR(30) NULL,
    email VARCHAR(255) NULL,
    adresse VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_clients_nom (nom),
    INDEX idx_clients_telephone (telephone),
    INDEX idx_clients_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. RENDEZ_VOUS
-- Table principale des rendez-vous
-- ============================================================

CREATE TABLE rendez_vous (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    client_id BIGINT UNSIGNED NOT NULL,
    cree_par BIGINT UNSIGNED NOT NULL,
    chef_id BIGINT UNSIGNED NOT NULL,

    date_rendez_vous DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,

    objet VARCHAR(255) NOT NULL,
    description TEXT NULL,

    statut ENUM(
        'planifie',
        'confirme',
        'termine',
        'annule'
    ) NOT NULL DEFAULT 'planifie',

    rappel_active BOOLEAN NOT NULL DEFAULT TRUE,
    rappel_minutes INT UNSIGNED NOT NULL DEFAULT 30,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_rdv_client
        FOREIGN KEY (client_id)
        REFERENCES clients(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_rdv_createur
        FOREIGN KEY (cree_par)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_rdv_chef
        FOREIGN KEY (chef_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_rdv_client (client_id),
    INDEX idx_rdv_createur (cree_par),
    INDEX idx_rdv_chef_date (chef_id, date_rendez_vous),
    INDEX idx_rdv_date (date_rendez_vous),
    INDEX idx_rdv_statut (statut),
    INDEX idx_rdv_date_heure (date_rendez_vous, heure_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. USER_DEVICES
-- Appareils et tokens Firebase FCM
-- ============================================================

CREATE TABLE user_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    fcm_token TEXT NOT NULL,
    plateforme ENUM('android', 'ios') NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    derniere_connexion TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_devices_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_devices_user (user_id),
    INDEX idx_devices_actif (actif),
    INDEX idx_devices_user_actif (user_id, actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. RAPPELS
-- Rappels à envoyer pour les rendez-vous
-- ============================================================

CREATE TABLE rappels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    rendez_vous_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    date_prevue TIMESTAMP NOT NULL,

    statut ENUM(
        'en_attente',
        'envoye',
        'echec',
        'annule'
    ) NOT NULL DEFAULT 'en_attente',

    date_envoi TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_rappels_rdv
        FOREIGN KEY (rendez_vous_id)
        REFERENCES rendez_vous(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_rappels_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_rappels_rdv (rendez_vous_id),
    INDEX idx_rappels_user (user_id),
    INDEX idx_rappels_statut_date (statut, date_prevue),
    INDEX idx_rappels_date_prevue (date_prevue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. NOTIFICATIONS
-- Historique des notifications
-- ============================================================

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,
    rendez_vous_id BIGINT UNSIGNED NULL,

    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,

    type ENUM(
        'rappel_rendez_vous',
        'nouveau_rendez_vous',
        'modification_rendez_vous',
        'annulation_rendez_vous'
    ) NOT NULL,

    lue BOOLEAN NOT NULL DEFAULT FALSE,
    date_envoi TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_notifications_rdv
        FOREIGN KEY (rendez_vous_id)
        REFERENCES rendez_vous(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_rdv (rendez_vous_id),
    INDEX idx_notifications_user_lue (user_id, lue),
    INDEX idx_notifications_date (date_envoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DONNEES DE TEST
-- ============================================================

-- Mot de passe de test :
-- "password"
-- Hash bcrypt Laravel compatible.
INSERT INTO users
    (id, nom, prenom, email, telephone, password, role, actif)
VALUES
    (
        1,
        'Kalonji',
        'Benjamin',
        'benjamin@gmail.com',
        '+243810000001',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqK9X6J9Y5VZ9V6Q2K',
        'chef',
        TRUE
    ),
    (
        2,
        'Kabeya',
        'Marie',
        'marie@gmail.com',
        '+243810000002',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCqK9X6J9Y5VZ9V6Q2K',
        'secretaire',
        TRUE
    );


INSERT INTO clients
    (id, nom, prenom, telephone, email, adresse)
VALUES
    (
        1,
        'Kalonji',
        'Jean',
        '+243810000003',
        'jean.kalonji@gmail.com',
        'Kinshasa, RDC'
    ),
    (
        2,
        'Mukendi',
        'Paul',
        '+243810000004',
        'paul.mukendi@gmail.com',
        'Kinshasa, RDC'
    );


-- Rendez-vous de test.
-- Date choisie : 30 septembre 2026 à 16h00.
INSERT INTO rendez_vous
    (
        id,
        client_id,
        cree_par,
        chef_id,
        date_rendez_vous,
        heure_debut,
        heure_fin,
        objet,
        description,
        statut,
        rappel_active,
        rappel_minutes
    )
VALUES
    (
        1,
        1,
        2,
        1,
        '2026-09-30',
        '16:00:00',
        '17:00:00',
        'Réunion',
        'Réunion de test avec le client Jean Kalonji.',
        'planifie',
        TRUE,
        30
    );


-- Appareil de test du chef.
-- Remplacer le token par un vrai token Firebase FCM
-- lorsque l'application Flutter sera connectée à Firebase.
INSERT INTO user_devices
    (
        id,
        user_id,
        fcm_token,
        plateforme,
        actif,
        derniere_connexion
    )
VALUES
    (
        1,
        1,
        'REMPLACER_PAR_UN_VRAI_TOKEN_FCM',
        'android',
        TRUE,
        CURRENT_TIMESTAMP
    );


-- Rappel de test.
-- 30 minutes avant le rendez-vous de 16h00 = 15h30.
INSERT INTO rappels
    (
        id,
        rendez_vous_id,
        user_id,
        date_prevue,
        statut
    )
VALUES
    (
        1,
        1,
        1,
        '2026-09-30 15:30:00',
        'en_attente'
    );


-- Notification de test.
INSERT INTO notifications
    (
        id,
        user_id,
        rendez_vous_id,
        titre,
        message,
        type,
        lue,
        date_envoi
    )
VALUES
    (
        1,
        1,
        1,
        'Rappel de rendez-vous',
        'Vous avez un rendez-vous avec Jean Kalonji à 16h00.',
        'rappel_rendez_vous',
        FALSE,
        NULL
    );


-- ============================================================
-- VERIFICATIONS
-- ============================================================

-- Voir les utilisateurs
SELECT id, nom, prenom, email, role, actif
FROM users;

-- Voir les clients
SELECT id, nom, prenom, telephone, email
FROM clients;

-- Voir les rendez-vous avec le client et le chef
SELECT
    rv.id,
    CONCAT(c.prenom, ' ', c.nom) AS client,
    CONCAT(u.prenom, ' ', u.nom) AS chef,
    rv.date_rendez_vous,
    rv.heure_debut,
    rv.heure_fin,
    rv.objet,
    rv.statut,
    rv.rappel_minutes
FROM rendez_vous rv
INNER JOIN clients c ON c.id = rv.client_id
INNER JOIN users u ON u.id = rv.chef_id;

-- Voir les rappels en attente
SELECT *
FROM rappels
WHERE statut = 'en_attente'
ORDER BY date_prevue ASC;

-- Voir les notifications du chef
SELECT *
FROM notifications
WHERE user_id = 1
ORDER BY created_at DESC;
