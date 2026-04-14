<?php
require_once 'Config/database.php';

class Ingredient {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── Ingrédients d'une recette ─────────────────────────────────────────────
    public function getByRecette(int $recetteId): array {
        $stmt = $this->db->prepare("SELECT * FROM ingredient WHERE recette_id = ?");
        $stmt->execute([$recetteId]);
        return $stmt->fetchAll();
    }

    // ── Un ingrédient par ID ──────────────────────────────────────────────────
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM ingredient WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ── Ajouter un ingrédient ─────────────────────────────────────────────────
    public function create(array $data): bool {
        $sql = "INSERT INTO ingredient (recette_id, nom, quantite, unite)
                VALUES (:recette_id, :nom, :quantite, :unite)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':recette_id' => (int)$data['recette_id'],
            ':nom'        => $data['nom'],
            ':quantite'   => (float)$data['quantite'],
            ':unite'      => $data['unite'],
        ]);
    }

    // ── Modifier un ingrédient ────────────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $sql = "UPDATE ingredient SET nom=:nom, quantite=:quantite, unite=:unite WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'      => $data['nom'],
            ':quantite' => (float)$data['quantite'],
            ':unite'    => $data['unite'],
            ':id'       => $id,
        ]);
    }

    // ── Supprimer un ingrédient ───────────────────────────────────────────────
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM ingredient WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ── Supprimer tous les ingrédients d'une recette ──────────────────────────
    public function deleteByRecette(int $recetteId): bool {
        $stmt = $this->db->prepare("DELETE FROM ingredient WHERE recette_id = ?");
        return $stmt->execute([$recetteId]);
    }
}
