-- Table recette — à exécuter dans phpMyAdmin ou via MySQL
CREATE TABLE IF NOT EXISTS `recette` (
    `id`                INT(11) NOT NULL AUTO_INCREMENT,
    `nom`               VARCHAR(150) NOT NULL,
    `description`       TEXT DEFAULT NULL,
    `ingredients`       TEXT DEFAULT NULL,
    `categorie`         ENUM('entrée','plat principal','dessert','boisson','snack','autre') DEFAULT NULL,
    `temps_preparation` INT(11) DEFAULT NULL,
    `calories`          INT(11) DEFAULT NULL,
    `image`             VARCHAR(255) DEFAULT NULL,
    `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
