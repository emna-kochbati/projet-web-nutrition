<?php
require_once 'Config/database.php';

class Meal {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT m.*, r.nom AS restaurant_nom
                FROM meal m
                JOIN restaurant r ON r.id = m.restaurant_id
                ORDER BY m.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getByRestaurant(int $restaurantId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM meal WHERE restaurant_id = ? ORDER BY categorie, nom"
        );
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT m.*, r.nom AS restaurant_nom
             FROM meal m JOIN restaurant r ON r.id = m.restaurant_id
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO meal (restaurant_id, nom, description, prix, categorie, calories, disponible, image)
                VALUES (:restaurant_id, :nom, :description, :prix, :categorie, :calories, :disponible, :image)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':restaurant_id' => (int)$data['restaurant_id'],
            ':nom'           => $data['nom'],
            ':description'   => $data['description'] ?? null,
            ':prix'          => (float)$data['prix'],
            ':categorie'     => $data['categorie'],
            ':calories'      => isset($data['calories']) ? (int)$data['calories'] : null,
            ':disponible'    => isset($data['disponible']) ? 1 : 0,
            ':image'         => $data['image'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE meal
                SET restaurant_id=:restaurant_id, nom=:nom, description=:description,
                    prix=:prix, categorie=:categorie, calories=:calories,
                    disponible=:disponible, image=:image
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':restaurant_id' => (int)$data['restaurant_id'],
            ':nom'           => $data['nom'],
            ':description'   => $data['description'] ?? null,
            ':prix'          => (float)$data['prix'],
            ':categorie'     => $data['categorie'],
            ':calories'      => isset($data['calories']) ? (int)$data['calories'] : null,
            ':disponible'    => isset($data['disponible']) ? 1 : 0,
            ':image'         => $data['image'] ?? null,
            ':id'            => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM meal WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteByRestaurant(int $restaurantId): bool {
        $stmt = $this->db->prepare("DELETE FROM meal WHERE restaurant_id = ?");
        return $stmt->execute([$restaurantId]);
    }

    public function search(string $q): array {
        $stmt = $this->db->prepare(
            "SELECT m.*, r.nom AS restaurant_nom
             FROM meal m JOIN restaurant r ON r.id = m.restaurant_id
             WHERE m.nom LIKE ? ORDER BY m.created_at DESC"
        );
        $stmt->execute(['%'.$q.'%']);
        return $stmt->fetchAll();
    }
}
