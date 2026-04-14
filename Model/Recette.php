<?php
require_once 'Config/database.php';

class Recette {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM recette ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO recette (nom, description, ingredients, categorie, temps_preparation, calories, image)
                VALUES (:nom, :description, :ingredients, :categorie, :temps_preparation, :calories, :image)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom'               => $data['nom'],
            ':description'       => $data['description'] ?? null,
            ':ingredients'       => $data['ingredients'] ?? null,
            ':categorie'         => $data['categorie'] ?? null,
            ':temps_preparation' => isset($data['temps_preparation']) && $data['temps_preparation'] !== '' ? (int)$data['temps_preparation'] : null,
            ':calories'          => isset($data['calories']) && $data['calories'] !== '' ? (int)$data['calories'] : null,
            ':image'             => $data['image'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE recette
                SET nom=:nom, description=:description, ingredients=:ingredients,
                    categorie=:categorie, temps_preparation=:temps_preparation,
                    calories=:calories, image=:image
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'               => $data['nom'],
            ':description'       => $data['description'] ?? null,
            ':ingredients'       => $data['ingredients'] ?? null,
            ':categorie'         => $data['categorie'] ?? null,
            ':temps_preparation' => isset($data['temps_preparation']) && $data['temps_preparation'] !== '' ? (int)$data['temps_preparation'] : null,
            ':calories'          => isset($data['calories']) && $data['calories'] !== '' ? (int)$data['calories'] : null,
            ':image'             => $data['image'] ?? null,
            ':id'                => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM recette WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(string $q): array {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE nom LIKE ? OR categorie LIKE ? ORDER BY created_at DESC");
        $stmt->execute(['%'.$q.'%', '%'.$q.'%']);
        return $stmt->fetchAll();
    }
}
