<?php
require_once 'Config/database.php';

// ── Entité Ingredient ─────────────────────────────────────────────────────────
class Ingredient {

    private ?int    $id;
    private ?string $nom;
    private ?string $type;
    private ?string $image;
    private ?string $createdAt;

    // ── Constructeur ──────────────────────────────────────────────────────────
    public function __construct(
        ?int    $id        = null,
        ?string $nom       = null,
        ?string $type      = null,
        ?string $image     = null,
        ?string $createdAt = null
    ) {
        $this->id        = $id;
        $this->nom       = $nom;
        $this->type      = $type;
        $this->image     = $image;
        $this->createdAt = $createdAt;
    }

    // ── Getters ───────────────────────────────────────────────────────────────
    public function getId(): ?int       { return $this->id; }
    public function getNom(): ?string   { return $this->nom; }
    public function getType(): ?string  { return $this->type; }
    public function getImage(): ?string { return $this->image; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // ── Setters ───────────────────────────────────────────────────────────────
    public function setId(?int $id): void       { $this->id = $id; }
    public function setNom(?string $n): void    { $this->nom = $n; }
    public function setType(?string $t): void   { $this->type = $t; }
    public function setImage(?string $i): void  { $this->image = $i; }
    public function setCreatedAt(?string $c): void { $this->createdAt = $c; }

    // ── Convertir un tableau PDO en objet Ingredient ──────────────────────────
    public static function fromArray(array $row): self {
        return new self(
            (int)$row['id'],
            $row['nom'],
            $row['type']       ?? null,
            $row['image']      ?? null,
            $row['created_at'] ?? null
        );
    }

    // ── Convertir en tableau (pour les vues) ──────────────────────────────────
    public function toArray(): array {
        return [
            'id'         => $this->id,
            'nom'        => $this->nom,
            'type'       => $this->type,
            'image'      => $this->image,
            'created_at' => $this->createdAt,
        ];
    }

    // =========================================================================
    // Méthodes d'accès à la base de données (PDO)
    // =========================================================================

    public function getAll(): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM ingredient ORDER BY nom ASC");
            $query->execute();
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function getById(int $id): array|false {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM ingredient WHERE id = :id");
            $query->execute([':id' => $id]);
            $row = $query->fetch();
            return $row ? self::fromArray($row)->toArray() : false;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function create(array $d): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare(
                "INSERT INTO ingredient (nom, type, image) VALUES (:nom, :type, :image)"
            );
            return $query->execute([
                ':nom'   => $d['nom'],
                ':type'  => $d['type'],
                ':image' => $d['image'] ?? null,
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function update(int $id, array $d): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare(
                "UPDATE ingredient SET nom=:nom, type=:type, image=:image WHERE id=:id"
            );
            return $query->execute([
                ':nom'   => $d['nom'],
                ':type'  => $d['type'],
                ':image' => $d['image'] ?? null,
                ':id'    => $id,
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete(int $id): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("DELETE FROM ingredient WHERE id = :id");
            return $query->execute([':id' => $id]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function search(string $q): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM ingredient WHERE nom LIKE :q ORDER BY nom ASC");
            $query->execute([':q' => '%' . $q . '%']);
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function filter(string $search, string $type): array {
        try {
            $pdo        = Database::getConnection();
            $conditions = [];
            $params     = [];
            if ($search !== '') {
                $conditions[] = 'nom LIKE :search';
                $params[':search'] = '%' . $search . '%';
            }
            if ($type !== '') {
                $conditions[] = 'type = :type';
                $params[':type'] = $type;
            }
            $sql = "SELECT * FROM ingredient";
            if (!empty($conditions)) $sql .= " WHERE " . implode(' AND ', $conditions);
            $sql .= " ORDER BY nom ASC";
            $query = $pdo->prepare($sql);
            $query->execute($params);
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function countByType(): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT type, COUNT(*) as total FROM ingredient GROUP BY type");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // ── Table de liaison recette_ingredient (jointure) ────────────────────────
    public function getByRecette(int $recetteId): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("
                SELECT ri.id, ri.quantite, ri.unite,
                       i.id AS ingredient_id, i.nom, i.type
                FROM recette_ingredient ri
                JOIN ingredient i ON i.id = ri.ingredient_id
                WHERE ri.recette_id = :recette_id
                ORDER BY i.nom ASC
            ");
            $query->execute([':recette_id' => $recetteId]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function addToRecette(int $recetteId, int $ingredientId, float $quantite, string $unite): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("
                INSERT INTO recette_ingredient (recette_id, ingredient_id, quantite, unite)
                VALUES (:recette_id, :ingredient_id, :quantite, :unite)
            ");
            return $query->execute([
                ':recette_id'    => $recetteId,
                ':ingredient_id' => $ingredientId,
                ':quantite'      => $quantite,
                ':unite'         => $unite,
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteByRecette(int $recetteId): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("DELETE FROM recette_ingredient WHERE recette_id = :id");
            return $query->execute([':id' => $recetteId]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // ── Jointure : ingrédients d'une recette avec nom de la recette ───────────
    public function getIngredientsByRecetteAvecJointure(int $recetteId): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("
                SELECT r.nom AS recette_nom, i.nom AS ingredient_nom,
                       i.type, ri.quantite, ri.unite
                FROM recette_ingredient ri
                JOIN recette r    ON r.id = ri.recette_id
                JOIN ingredient i ON i.id = ri.ingredient_id
                WHERE ri.recette_id = :recette_id
                ORDER BY i.nom ASC
            ");
            $query->execute([':recette_id' => $recetteId]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
