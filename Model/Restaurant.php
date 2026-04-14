<?php
require_once 'Config/database.php';

class Restaurant {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM restaurant ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO restaurant (nom, description, adresse, telephone, email, type_cuisine, capacite, image)
                VALUES (:nom, :description, :adresse, :telephone, :email, :type_cuisine, :capacite, :image)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':description'  => $data['description'] ?? null,
            ':adresse'      => $data['adresse'],
            ':telephone'    => $data['telephone'] ?? null,
            ':email'        => $data['email'] ?? null,
            ':type_cuisine' => $data['type_cuisine'],
            ':capacite'     => isset($data['capacite']) ? (int)$data['capacite'] : null,
            ':image'        => $data['image'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE restaurant
                SET nom=:nom, description=:description, adresse=:adresse,
                    telephone=:telephone, email=:email, type_cuisine=:type_cuisine,
                    capacite=:capacite, image=:image
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'          => $data['nom'],
            ':description'  => $data['description'] ?? null,
            ':adresse'      => $data['adresse'],
            ':telephone'    => $data['telephone'] ?? null,
            ':email'        => $data['email'] ?? null,
            ':type_cuisine' => $data['type_cuisine'],
            ':capacite'     => isset($data['capacite']) ? (int)$data['capacite'] : null,
            ':image'        => $data['image'] ?? null,
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM restaurant WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(string $q): array {
        $stmt = $this->db->prepare("SELECT * FROM restaurant WHERE nom LIKE ? OR adresse LIKE ? ORDER BY created_at DESC");
        $stmt->execute(['%'.$q.'%', '%'.$q.'%']);
        return $stmt->fetchAll();
    }
}
