-- Ingredient = catalogue (sans recette). Les quantités sont dans recette_ingredient.
ALTER TABLE `ingredient`
  DROP FOREIGN KEY `ingredient_ibfk_1`,
  DROP COLUMN `recette_id`,
  DROP COLUMN `quantite`,
  DROP COLUMN `unite`;
