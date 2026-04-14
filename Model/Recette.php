<?php
require_once 'Config/database.php';

class Recette {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM recette ORDER BY created_at DESC")->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO recette (nom, categorie, duree, difficulte, calories, image)
             VALUES (:nom, :categorie, :duree, :difficulte, :calories, :image)"
        );
        $stmt->execute([
            ':nom'        => $d['nom'],
            ':categorie'  => $d['categorie'],
            ':duree'      => $d['duree'],
            ':difficulte' => $d['difficulte'],
            ':calories'   => $d['calories'],
            ':image'      => $d['image'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            "UPDATE recette SET nom=:nom, categorie=:categorie, duree=:duree,
             difficulte=:difficulte, calories=:calories, image=:image WHERE id=:id"
        );
        return $stmt->execute([
            ':nom'        => $d['nom'],
            ':categorie'  => $d['categorie'],
            ':duree'      => $d['duree'],
            ':difficulte' => $d['difficulte'],
            ':calories'   => $d['calories'],
            ':image'      => $d['image'] ?? null,
            ':id'         => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM recette WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(string $q): array {
        $stmt = $this->db->prepare("SELECT * FROM recette WHERE nom LIKE ? ORDER BY created_at DESC");
        $stmt->execute(['%' . $q . '%']);
        return $stmt->fetchAll();
    }
}
