-- ============================================================
--  NutriSmart — Base de données
--  Base : nutrition_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS `nutrition_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `nutrition_db`;

-- ------------------------------------------------------------
-- Table : restaurant
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `nom`          VARCHAR(150) NOT NULL,
    `description`  TEXT         DEFAULT NULL,
    `adresse`      VARCHAR(255) NOT NULL,
    `telephone`    VARCHAR(30)  DEFAULT NULL,
    `email`        VARCHAR(150) DEFAULT NULL,
    `type_cuisine` ENUM('tunisienne','italienne','japonaise','americaine','indienne','mexicaine','française','autre') NOT NULL,
    `image`        VARCHAR(255) DEFAULT NULL,
    `latitude`     DECIMAL(10,7) DEFAULT NULL,
    `longitude`    DECIMAL(10,7) DEFAULT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table : meal
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `meal` (
    `id`            INT(11)        NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT(11)        NOT NULL,
    `nom`           VARCHAR(150)   NOT NULL,
    `description`   TEXT           DEFAULT NULL,
    `prix`          DECIMAL(8,2)   NOT NULL DEFAULT 0.00,
    `categorie`     ENUM('entree','plat_principal','dessert','boisson','snack') NOT NULL,
    `calories`      INT(11)        DEFAULT NULL,
    `disponible`    TINYINT(1)     NOT NULL DEFAULT 1,
    `image`         VARCHAR(255)   DEFAULT NULL,
    `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_meal_restaurant`
        FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Données de test — restaurant
-- ------------------------------------------------------------
INSERT INTO `restaurant` (`nom`, `description`, `adresse`, `telephone`, `email`, `type_cuisine`, `capacite`) VALUES
('Le Jasmin',    'Restaurant tunisien traditionnel au cœur de la médina.', '12 Rue de la Médina, Tunis',     '+216 71 000 001', 'jasmin@resto.tn', 'tunisienne', 36.8190, 10.1658),
('Bella Italia', 'Authentique cuisine italienne, pizzas et pâtes maison.', '5 Avenue Habib Bourguiba, Tunis', '+216 71 000 002', 'bella@resto.tn',  'italienne',  36.8008, 10.1800),
('Tokyo Garden', 'Sushis, ramens et spécialités japonaises.',              '8 Rue de Marseille, Tunis',      '+216 71 000 003', 'tokyo@resto.tn',  'japonaise',  36.8065, 10.1815);

-- ------------------------------------------------------------
-- Données de test — meal
-- ------------------------------------------------------------
INSERT INTO `meal` (`restaurant_id`, `nom`, `description`, `prix`, `categorie`, `calories`, `disponible`) VALUES
(1, 'Couscous Agneau',  'Couscous traditionnel avec légumes et agneau.', 18.00, 'plat_principal', 650, 1),
(1, 'Brik à l\'œuf',   'Brik croustillant garni d\'œuf et de thon.',     7.50, 'entree',         320, 1),
(1, 'Makroudh',         'Gâteau aux dattes et à la semoule.',             4.00, 'dessert',        280, 1),
(2, 'Pizza Margherita', 'Tomate, mozzarella, basilic frais.',            14.00, 'plat_principal', 520, 1),
(2, 'Tiramisu',         'Dessert italien au café et mascarpone.',         6.50, 'dessert',        380, 1),
(3, 'Sushi Mix 12 pcs', 'Assortiment de sushis variés.',                 22.00, 'plat_principal', 420, 1),
(3, 'Ramen Tonkotsu',   'Bouillon de porc, nouilles, œuf mollet.',       16.00, 'plat_principal', 580, 1);

-- Ajouter latitude/longitude si pas encore présentes
ALTER TABLE `restaurant` ADD COLUMN IF NOT EXISTS `latitude`  DECIMAL(10,7) DEFAULT NULL;
ALTER TABLE `restaurant` ADD COLUMN IF NOT EXISTS `longitude` DECIMAL(10,7) DEFAULT NULL;

-- ------------------------------------------------------------
-- Table : avis (notation des restaurants)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `avis` (
    `id`            INT(11)   NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT(11)   NOT NULL,
    `note`          TINYINT(1) NOT NULL CHECK (`note` BETWEEN 1 AND 5),
    `ip`            VARCHAR(45) DEFAULT NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_avis_restaurant`
        FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant`(`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
