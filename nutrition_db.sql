-- ============================================
-- Base de données : nutrition_db
-- Module : Recette
-- ============================================

CREATE DATABASE IF NOT EXISTS nutrition_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nutrition_db;

-- Table Recette
CREATE TABLE IF NOT EXISTS recette (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150)  NOT NULL,
    description TEXT          NOT NULL,
    categorie   ENUM('petit-dejeuner','dejeuner','diner','collation','dessert','vegetarien','regime','sportif') NOT NULL,
    duree       INT           NOT NULL COMMENT 'durée en minutes',
    difficulte  ENUM('facile','moyen','difficile') NOT NULL,
    calories    INT           NOT NULL,
    image       VARCHAR(255)  DEFAULT NULL,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table Ingredient
CREATE TABLE IF NOT EXISTS ingredient (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    recette_id  INT           NOT NULL,
    nom         VARCHAR(150)  NOT NULL,
    quantite    DECIMAL(8,2)  NOT NULL,
    unite       VARCHAR(50)   NOT NULL,
    FOREIGN KEY (recette_id) REFERENCES recette(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Données de test
INSERT INTO recette (nom, description, categorie, duree, difficulte, calories) VALUES
('Salade Verte Énergisante',  'Une salade fraîche riche en vitamines et minéraux.',          'dejeuner',       15, 'facile',  180),
('Smoothie Protéiné',         'Smoothie banane-épinards idéal après le sport.',              'collation',       5, 'facile',  220),
('Bowl de Quinoa',            'Bowl complet avec quinoa, légumes rôtis et sauce tahini.',    'diner',          30, 'moyen',   450);

INSERT INTO ingredient (recette_id, nom, quantite, unite) VALUES
(1, 'Laitue',    100, 'g'),
(1, 'Tomates',     2, 'pièce(s)'),
(1, 'Concombre',   1, 'pièce(s)'),
(2, 'Banane',      1, 'pièce(s)'),
(2, 'Épinards',   50, 'g'),
(2, 'Lait d\'amande', 200, 'ml'),
(3, 'Quinoa',    150, 'g'),
(3, 'Courgette',   1, 'pièce(s)'),
(3, 'Tahini',     30, 'ml');
